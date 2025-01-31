<?php

namespace App\Utils\Type;

enum DumpType: string {
    public const VAR_DUMPER = 'var_dumper';
    public const VAR_DUMP = 'var_dump';
    public const VAR_EXPORT = 'var_export';
    public const PRINT_R = 'print_r';
    public const YAML = 'yaml';
}
