<?php

namespace Yoyo\Modules\Yoyo;

use Clickfwd\Yoyo\Component;
use Joomla\CMS\Factory;

class Upload extends Component
{
    protected $error;

    protected $request;

    public function mount()
    {
        $this->request = Factory::getApplication()->input;
    }

    protected function getImageProperty()
    {
        $photo = $this->request->files->get('photo',[],'array');
        
        if (empty($photo)) {
            return '';
        }

        if (! $this->isImage($photo)) {
            $this->error = 'Not a valid image';

            return;
        }

        $tmp = $photo['tmp_name'];

        $str = file_get_contents($tmp);

        return base64_encode($str);
    }

    protected function getErrorProperty()
    {
        return $this->error;
    }

    protected function isImage($file)
    {
        $whitelist_type = ['image/jpeg', 'image/png', 'image/gif'];

        $error = null;

        $fileinfo = finfo_open(FILEINFO_MIME_TYPE);

        if (in_array(finfo_file($fileinfo, $file['tmp_name']), $whitelist_type)) {
            return true;
        }

        return false;
    }
}
