<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ConvertExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ConvertExtension extends AbstractExtension {
    public function getFilters(): array {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFilter('convert_var_dump', [ConvertExtensionRuntime::class, 'varDump']),
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFilter('convert_var_export', [ConvertExtensionRuntime::class, 'varExport']),
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFilter('convert_print_r', [ConvertExtensionRuntime::class, 'printR']),
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFilter('convert_json_encode', [ConvertExtensionRuntime::class, 'jsonEncode']),
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFilter('convert_serialize', [ConvertExtensionRuntime::class, 'phpSerialize']),
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFilter('convert_yaml_dump', [ConvertExtensionRuntime::class, 'yamlDump']),
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFilter('convert_url', [ConvertExtensionRuntime::class, 'urlEncoding']),
        ];
    }

    public function getFunctions(): array {
        return [
            /* @psalm-suppress InvalidArgument Incorrect reflection in Twig */
            new TwigFunction('convert_var_dumper', [ConvertExtensionRuntime::class, 'varDumper'], ['is_safe' => ['html'], 'needs_context' => true, 'needs_environment' => true]),
        ];
    }
}
