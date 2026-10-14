<?php

namespace App\Authorization\Catalog;

enum Layer: string
{
    case Page = 'page';
    case Section = 'section';
    case Action = 'action';
    case Field = 'field';
}
