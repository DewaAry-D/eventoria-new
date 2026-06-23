<?php

namespace App\Enums;
enum EventStatus: string {
    case DRAFT = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case REVISION = 'revision';
    case PUBLISHED = 'published';
    case COMPLETED = 'completed';
}