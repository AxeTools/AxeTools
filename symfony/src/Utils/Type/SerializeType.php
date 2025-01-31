<?php

namespace App\Utils\Type;

enum SerializeType {
    public const TYPE_JSON = 'json_encode';
    public const TYPE_SERIALIZE = 'serialize';
    public const TYPE_YAML = 'yaml';
    public const TYPE_URL = 'url';
}
