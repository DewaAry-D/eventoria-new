<?php

namespace App\Enums;

enum FieldType: string {
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case URL = 'url';
    case RADIO = 'radio';
    case CHECKBOX = 'checkbox';
    case SELECT = 'select';
    case FILE_PDF = 'file_pdf';
    case FILE_IMAGE = 'file_image';
}