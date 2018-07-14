<?php

namespace APPelit\LaraSauce;

use EventSauce\EventSourcing\CodeGeneration\DefinitionGroup;
use EventSauce\EventSourcing\CodeGeneration\DefinitionWithFields;
use EventSauce\EventSourcing\CodeGeneration\EventDefinition;

class Psr4CodeDumper
{
    /** @var string */
    private $outputPath;

    /** @var DefinitionGroup */
    private $definitionGroup;

    /** @var bool */
    private $withHelpers;

    /** @var bool */
    private $withSerialization;

    /** @var string */
    private $namespace;

    /**
     * Psr4CodeDumper constructor.
     * @param string $outputPath
     * @param DefinitionGroup $definitionGroup
     * @param bool $withHelpers
     * @param bool $withSerialization
     */
    public function __construct(string $outputPath,
                                DefinitionGroup $definitionGroup,
                                bool $withHelpers = true,
                                bool $withSerialization = true)
    {
        $this->outputPath = $outputPath;
        $this->definitionGroup = $definitionGroup;
        $this->withHelpers = $withHelpers;
        $this->withSerialization = $withSerialization;

        $this->namespace = $definitionGroup->namespace();

        if (strpos($this->outputPath, '/') !== 0) {
            $this->outputPath = app_path($this->outputPath);
        }
    }

    public function dump()
    {
        $this->dumpEvents();
        $this->dumpCommands();
    }

    /**
     * @param string $namespace
     * @param string $className
     * @param string $code
     */
    protected function saveFile(string $namespace, string $className, string $code)
    {
        $path = $this->outputPath . DIRECTORY_SEPARATOR;
        $path .= implode(DIRECTORY_SEPARATOR, array_slice(explode('\\', $namespace), 1));

        if (!file_exists($path)) {
            if (!@mkdir($path, 0750, true)) {
                throw new \RuntimeException("Unable to create storage folder for {$namespace}\\{$className} at {$path}");
            }
        }

        $path .= DIRECTORY_SEPARATOR . "{$className}.php";

        if (@file_put_contents($path, rtrim($code)) === false) {
            throw new \RuntimeException("Could not write generated code for {$namespace}\\{$className} to {$path}");
        }
    }

    protected function dumpEvents()
    {
        $events = $this->definitionGroup->events();

        if (empty($events)) {
            return;
        }

        $namespace = "{$this->namespace}\\Events";

        $header = "<?php\n\nnamespace $namespace";

        if ($this->withSerialization) {
            $header .= ";\n\nuse EventSauce\EventSourcing\Serialization\SerializableEvent";
        }

        $header .= ";\n\n";

        foreach ($events as $event) {
            $name = $event->name();
            $fields = $this->dumpFields($event);
            $constructor = $this->dumpConstructor($event);
            $methods = $this->dumpMethods($event);
            $deserializer = $this->dumpSerializationMethods($event);
            $testHelpers = $this->withHelpers ? $this->dumpTestHelpers($event) : '';
            $implements = $this->withSerialization ? ' implements SerializableEvent' : '';

            $code = <<<EOF
{$header}final class {$name}{$implements}
{
{$fields}{$constructor}{$methods}{$deserializer}

{$testHelpers}}


EOF;
            $this->saveFile($namespace, $name, $code);
        }
    }

    /**
     * @param DefinitionWithFields $definition
     * @return string
     */
    protected function dumpFields(DefinitionWithFields $definition): string
    {
        $fields = $this->fieldsFromDefinition($definition);
        $code = [];
        $code[] = <<<EOF

EOF;

        foreach ($fields as $field) {
            $name = $field['name'];
            $type = $this->definitionGroup->resolveTypeAlias($field['type']);

            $code[] = <<<EOF
    /**
     * @var $type
     */
    private \$$name;


EOF;
        }

        return implode('', $code);
    }

    /**
     * @param DefinitionWithFields $definition
     * @return string
     */
    protected function dumpConstructor(DefinitionWithFields $definition): string
    {
        $arguments = [];
        $argumentDocBlock = '';
        $assignments = [];
        $fields = $this->fieldsFromDefinition($definition);

        if (empty($fields)) {
            return '';
        }

        foreach ($fields as $field) {
            $resolvedType = $this->definitionGroup->resolveTypeAlias($field['type']);
            $arguments[] = sprintf('        %s $%s', $resolvedType, $field['name']);
            $assignments[] = sprintf('        $this->%s = $%s;', $field['name'], $field['name']);
            $argumentDocBlock .= sprintf("     * @param %s $%s\n", $resolvedType, $field['name']);
        }

        $arguments = implode(",\n", $arguments);
        $assignments = implode("\n", $assignments);

        return <<<EOF
    /**
{$argumentDocBlock}     */
    public function __construct(
$arguments
    ) {
$assignments
    }


EOF;
    }

    /**
     * @param DefinitionWithFields $command
     * @return string
     */
    protected function dumpMethods(DefinitionWithFields $command): string
    {
        $methods = [];

        foreach ($this->fieldsFromDefinition($command) as $field) {
            $methods[] = <<<EOF
    /**
     * @return {$this->definitionGroup->resolveTypeAlias($field['type'])}
     */
    public function {$field['name']}(): {$this->definitionGroup->resolveTypeAlias($field['type'])}
    {
        return \$this->{$field['name']};
    }


EOF;
        }

        return empty($methods) ? '' : rtrim(implode('', $methods)) . "\n";
    }

    /**
     * @param EventDefinition $event
     * @return string
     */
    protected function dumpSerializationMethods(EventDefinition $event)
    {
        $name = $event->name();
        $arguments = [];
        $serializers = [];

        foreach ($this->fieldsFromDefinition($event) as $field) {
            $type = $this->definitionGroup->resolveTypeAlias($field['type']);
            $parameter = sprintf('$payload[\'%s\']', $field['name']);
            $template = $event->deserializerForField($field['name'])
                ?: $event->deserializerForType($field['type']);
            $arguments[] = trim(strtr($template, ['{type}' => $type, '{param}' => $parameter]));

            $property = sprintf('$this->%s', $field['name']);
            $template = $event->serializerForField($field['name'])
                ?: $event->serializerForType($field['type']);
            $template = sprintf("'%s' => %s", $field['name'], $template);
            $serializers[] = trim(strtr($template, ['{type}' => $type, '{param}' => $property]));
        }

        $arguments = preg_replace('/^.{2,}$/m', '            $0', implode(",\n", $arguments));

        if (!empty($arguments)) {
            $arguments = "\n$arguments";
        }

        $serializers = preg_replace('/^.{2,}$/m', '            $0', implode(",\n", $serializers));

        if (!empty($serializers)) {
            $serializers = "\n$serializers,\n        ";
        }

        return <<<EOF

    /**
     * @param array \$payload
     * @return \EventSauce\EventSourcing\Serialization\SerializableEvent
     */
    public static function fromPayload(array \$payload): SerializableEvent
    {
        return new $name($arguments);
    }

    /**
     * @return array
     */
    public function toPayload(): array
    {
        return [$serializers];
    }
EOF;
    }

    /**
     * @param EventDefinition $event
     * @return string
     */
    protected function dumpTestHelpers(EventDefinition $event): string
    {
        $constructor = [];
        $constructorArguments = '';
        $constructorArgumentDocBlock = '';
        $constructorValues = [];
        $helpers = [];

        foreach ($this->fieldsFromDefinition($event) as $field) {
            $resolvedType = $this->definitionGroup->resolveTypeAlias($field['type']);

            if (null === $field['example']) {
                $constructor[] = ucfirst($field['name']);

                if ('' !== $constructorArguments) {
                    $constructorArguments .= ', ';
                }

                $constructorArguments .= sprintf('%s $%s', $resolvedType, $field['name']);
                $constructorArgumentDocBlock .= sprintf("     * @param %s $%s\n", $resolvedType, $field['name']);
                $constructorValues[] = sprintf('$%s', $field['name']);
            } else {
                $constructorValues[] = $this->dumpConstructorValue($field, $event);
                $method = sprintf('with%s', ucfirst($field['name']));
                $helpers[] = <<<EOF
    /**
     * @param {$resolvedType} \${$field['name']}
     * @return \$this
     * @codeCoverageIgnore
     */
    public function $method({$resolvedType} \${$field['name']}): {$event->name()}
    {
        \$this->{$field['name']} = \${$field['name']};

        return \$this;
    }


EOF;
            }
        }

        $constructor = sprintf('with%s', implode('And', $constructor));
        $constructorValues = implode(",\n            ", $constructorValues);

        if ('' !== $constructorValues) {
            $constructorValues = "\n            $constructorValues\n        ";
        }

        $helpers[] = <<<EOF
    /**
{$constructorArgumentDocBlock}     * @return \$this
     * @codeCoverageIgnore
     */
    public static function $constructor($constructorArguments): {$event->name()}
    {
        return new {$event->name()}($constructorValues);
    }


EOF;

        return rtrim(implode('', $helpers)) . "\n";
    }

    /**
     * @param array $field
     * @param EventDefinition $event
     * @return string
     */
    protected function dumpConstructorValue(array $field, EventDefinition $event): string
    {
        $parameter = rtrim($field['example']);
        $resolvedType = $this->definitionGroup->resolveTypeAlias($field['type']);

        if (gettype($parameter) === $resolvedType) {
            $parameter = var_export($parameter, true);
        }

        $template = $event->deserializerForField($field['name'])
            ?: $event->deserializerForType($field['type']);

        return rtrim(strtr($template, ['{type}' => $resolvedType, '{param}' => $parameter]));
    }

    protected function dumpCommands()
    {
        $commands = $this->definitionGroup->commands();

        if (empty($commands)) {
            return;
        }

        $namespace = "{$this->namespace}\\Commands";

        $header = "<?php\n\nnamespace $namespace;\n\n";

        foreach ($commands as $command) {
            $code = <<<EOF
{$header}final class {$command->name()}
{
{$this->dumpFields($command)}{$this->dumpConstructor($command)}{$this->dumpMethods($command)}}


EOF;
            $this->saveFile($namespace, $command->name(), $code);
        }
    }

    /**
     * @param DefinitionWithFields $definition
     * @return array
     */
    protected function fieldsFromDefinition(DefinitionWithFields $definition): array
    {
        $fields = $this->fieldsFrom($definition->fieldsFrom());

        foreach ($definition->fields() as $field) {
            array_push($fields, $field);
        }

        return $fields;
    }

    /**
     * @param string $fieldsFrom
     * @return array
     */
    protected function fieldsFrom(string $fieldsFrom): array
    {
        if (empty($fieldsFrom)) {
            return [];
        }

        foreach ($this->definitionGroup->events() as $event) {
            if ($event->name() === $fieldsFrom) {
                return $event->fields();
            }
        }

        foreach ($this->definitionGroup->commands() as $command) {
            if ($command->name() === $fieldsFrom) {
                return $command->fields();
            }
        }

        throw new \LogicException("Could not inherit fields from {$fieldsFrom}.");
    }
}
