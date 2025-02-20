<?php

namespace App\Utils\Type;

enum SerializeType {
    public const string TYPE_JSON = 'json_encode';
    public const string TYPE_SERIALIZE = 'serialize';
    public const string TYPE_YAML = 'yaml';
    public const string TYPE_URL = 'url';
}
