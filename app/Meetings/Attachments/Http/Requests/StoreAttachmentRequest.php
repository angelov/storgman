<?php

namespace Angelov\Eestec\Platform\Meetings\Attachments\Http\Requests;

use Angelov\Eestec\Platform\Core\Http\Request;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class StoreAttachmentRequest extends Request
{
    protected $config;

    protected $rules = [
        'file' => 'required'
    ];

    public function __construct(ConfigRepository $config, Store $session, Redirector $redirector)
    {
        parent::__construct($session, $redirector);
        $this->config = $config;

        $this->addMaxFileSizeRule();
    }

    public function response(array $errors)
    {
        $errors = $this->parseErrors($errors);
        return new JsonResponse($errors, 500);
    }

    private function addMaxFileSizeRule()
    {
        $default = 1500;

        $maxSize = $this->config->get('app.max_file_upload_size', $default);

        if (! is_numeric($maxSize)) {
            throw new \InvalidArgumentException(sprintf(
                "The value for max size for uploaded files must be numeric, but %s was provided.",
                gettype($maxSize)
            ));
        }

        $rule = "max:" . $maxSize;
        $this->addRule('file', $rule);
    }
}