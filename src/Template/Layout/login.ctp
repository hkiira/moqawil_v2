<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <!--begin::Fonts-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
        <!--end::Fonts-->
        <!--begin::Page Custom Styles(used by this page)-->
        <?= $this->Html->css('/assets/css/pages/login/login-4.css') ?>
        <!--end::Page Custom Styles-->
        <!--begin::Global Theme Styles(used by all pages)-->
        <?= $this->Html->css('/assets/plugins/global/plugins.bundle.css') ?>
        <?= $this->Html->css('/assets/plugins/custom/prismjs/prismjs.bundle.css') ?>
        <?= $this->Html->css('/assets/css/style.bundle.css') ?>
        <link href="" rel="stylesheet" type="text/css" />
        <!--end::Global Theme Styles-->
        <!--begin::Layout Themes(used by all pages)-->
        <?= $this->Html->css('/assets/css/themes/layout/header/base/light.css') ?>
        <?= $this->Html->css('/assets/css/themes/layout/header/menu/light.css') ?>
        <?= $this->Html->css('/assets/css/themes/layout/header/menu/light.css') ?>
        <?= $this->Html->css('/assets/css/themes/layout/brand/dark.css') ?>
        <?= $this->Html->css('/assets/css/themes/layout/aside/dark.css') ?>
        <!--end::Layout Themes-->
        <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    </head>
    <!--end::Head-->
    <!--begin::Body-->
    <body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <!--begin::Main-->
        <div class="d-flex flex-column flex-root min-vh-100 justify-content-center align-items-center py-5">
            <!--begin::Login-->
            <div class="login-modern w-100" style="max-width: 450px;">
                <!--begin::Container-->
                <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                    <!--begin::Card Body-->
                    <div class="card-body p-0">
                        <!--begin::Header Section-->
                        <div class="bg-gradient p-5 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <!--begin::Logo-->
                            <a href="#" class="d-inline-block mb-3">
                                <?= $this->Html->image('/logo-light.png') ?>
                            </a>
                            <!--end::Logo-->
                            <p class="text-white-50 mb-0">Connectez-vous à votre compte</p>
                        </div>
                        <!--end::Header Section-->
                        
                        <!--begin::Form Section-->
                        <div class="p-5">
                            <?= $this->Flash->render() ?>
                            <?= $this->fetch('content')  ?>
                        </div>
                        <!--end::Form Section-->
                    </div>
                    <!--end::Card Body-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Login-->
        </div>
        <?= $this->Html->script('/assets/plugins/global/plugins.bundle.js') ?>
        <?= $this->Html->script('/assets/plugins/custom/prismjs/prismjs.bundle.js') ?>
        <?= $this->Html->script('/assets/js/scripts.bundle.js') ?>
        <?= $this->Html->script('/assets/js/pages/custom/login/login-4.js') ?>
    </body>
    <!--end::Body-->
</html>