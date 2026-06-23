<?php

namespace App\Enums;

enum UserRole: string {
    case MAHASISWA = 'mahasiswa';
    case ORGANISASI = 'organisasi';
    case ADMIN_DPM = 'admin_dpm';
}