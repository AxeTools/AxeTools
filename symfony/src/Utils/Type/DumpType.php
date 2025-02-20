<?php

namespace App\Utils\Type;

enum DumpType: string {
    public const string VAR_DUMPER = 'var_dumper';
    public const string VAR_DUMP = 'var_dump';
    public const string VAR_EXPORT = 'var_export';
    public const string PRINT_R = 'print_r';
    public const string YAML = 'yaml';
}
