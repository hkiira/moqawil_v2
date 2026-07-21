<?php
namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper as CoreHtmlHelper;

class HtmlHelper extends CoreHtmlHelper
{
    public function image($path, array $options = [])
    {
        // Check if we are rendering a PDF request
        $isPdf = ($this->request->getParam('_ext') === 'pdf');
        
        if ($isPdf && (strpos($path, 'logo.jpg') !== false || strpos($path, 'logo.png') !== false || strpos($path, 'logo-light.png') !== false)) {
            $currentCompany = $this->_View->get('currentCompany');
            $logoFile = 'logo.jpg';
            
            if ($currentCompany && !empty($currentCompany->code)) {
                $companyCode = strtolower($currentCompany->code);
                $customPath = WWW_ROOT . 'img' . DS . 'companies' . DS . $companyCode . DS . 'logo.jpg';
                if (file_exists($customPath)) {
                    $logoFile = 'companies/' . $companyCode . '/logo.jpg';
                }
            }
            
            $absolutePath = WWW_ROOT . str_replace('/', DS, $logoFile);
            
            // Build raw HTML image tag with local filesystem path
            $attributes = [];
            foreach ($options as $key => $val) {
                if ($key !== 'escape') {
                    $attributes[] = h($key) . '="' . h($val) . '"';
                }
            }
            $attrsString = !empty($attributes) ? ' ' . implode(' ', $attributes) : '';
            
            return '<img src="' . $absolutePath . '"' . $attrsString . ' />';
        }
        
        return parent::image($path, $options);
    }
}
