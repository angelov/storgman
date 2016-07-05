<?php

namespace Angelov\Eestec\Platform\Meetings\Attachments\Http\Requests;

use Angelov\Eestec\Platform\Core\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreAttachmentRequest extends Request
{
    protected $rules = [
        'file' => 'required|max:1500'
    ];

    public function response(array $errors)
    {
        $errors = $this->parseErrors($errors);
        return new JsonResponse($errors, 500);
    }
}