<?php

namespace App\Twig\Runtime;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Template;

final class ConvertExtensionRuntime implements RuntimeExtensionInterface {
    public function __construct() {
        // Inject dependencies if needed
    }

    /**
     * Twig wrapper for serialize().
     */
    public static function phpSerialize(mixed $data): string {
        return serialize($data);
    }

    /**
     * Twig wrapper for json_encode().
     */
    public static function jsonEncode(mixed $data): string {
        $result = json_encode($data);
        if (!is_string($result)) {
            return '';
        }

        return $result;
    }

    /**
     * Twig wrapper for print_r().
     */
    public static function printR(mixed $data): string {
        return print_r($data, true);
    }

    /**
     * Twig wrapper for var_export().
     */
    public static function varExport(mixed $data): string {
        return var_export($data, true);
    }

    /**
     * Return the var_dump() output as a string.
     *
     * @psalm-suppress ForbiddenCode var_dump should not typically be allowed to run on production code
     */
    public static function varDump(mixed $data): string {
        ob_start();
        var_dump($data);
        $results = ob_get_contents();
        if (false === $results) {
            $results = '';
        }
        ob_end_clean();

        return $results;
    }

    /**
     * Return the HtmlDumper() output, this is a clone of the Twig dump() command that is not restricted by the dev
     * environment.
     *
     * @param array<mixed> $context
     */
    public static function varDumper(Environment $env, array $context): ?string {
        if (2 === \func_num_args()) {
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof Template) {
                    $vars[$key] = $value;
                }
            }

            $vars = [$vars];
        } else {
            $vars = \func_get_args();
            unset($vars[0], $vars[1]);
        }

        $dump = fopen('php://memory', 'r+');
        if (is_resource($dump)) {
            $dumper = new HtmlDumper();
            $dumper->setCharset($env->getCharset());
            $cloner = new VarCloner();
            foreach ($vars as $value) {
                $dumper->dump($cloner->cloneVar($value), $dump);
            }
            $results = stream_get_contents($dump, -1, 0);
            if (false === $results) {
                $results = null;
            }

            return $results;
        } else {
            return '';
        }
    }

    /**
     * Return the YAML version of the passed data.
     */
    public static function yamlDump(mixed $data): string {
        return Yaml::dump($data);
    }

    /**
     * Twig wrapper for html_build_query or urlencode depending on the type passed to the function.
     */
    public static function urlEncoding(mixed $data): string {
        if (is_array($data)) {
            return http_build_query($data);
        }

        return urlencode($data);
    }
}
