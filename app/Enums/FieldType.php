<?php

namespace App\Enums;

enum FieldType: string {
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case URL = 'url';
    case RADIO = 'radio';
    case SELECT = 'select';
    case FILE_PDF = 'file_pdf';
    case FILE_IMAGE = 'file_image';

    public function label(): string
    {
        return match($this) {
            self::TEXT => 'Teks Pendek',
            self::TEXTAREA => 'Paragraf',
            self::NUMBER => 'Angka',
            self::EMAIL => 'Email',
            self::URL => 'URL / Tautan (Cth: Portofolio/Github)',
            self::SELECT => 'Dropdown',
            self::RADIO => 'Pilihan Ganda (Radio)',
            self::FILE_PDF => 'Upload Dokumen (Hanya PDF)',
            self::FILE_IMAGE => 'Upload Gambar (JPG/PNG)',
        };
    }
}

