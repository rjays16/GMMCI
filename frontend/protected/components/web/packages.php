<?php

/** @var \SegHEIRS\components\AssetManager $assetManager */
$assetManager = \Yii::app()->getComponent('assetManager');
$ziiBasePath = $assetManager->publish(\Yii::getPathOfAlias('zii.widgets.assets'));
$appBaseUrl = \Yii::app()->getBaseUrl();

$packages = [
    'core' => [
        'js' => [
        ],
    ],

    'babel-polyfill' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/babel-polyfill/',
        'js' => [
            'dist/polyfill.min.js'
        ]
    ],

    'jquery' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/',
        'js' => [
            'lib/jquery/dist/jquery.js',
            'lib/jquery-migrate/dist/jquery-migrate.js',
            'app/global/plugins/jquery.livequery.min.js'
        ],
    ],

    'jquery.ui' => [
        'js' => [ 'jui/js/jquery-ui.min.js' ],
        'depends' => [ 'jquery' ],
    ],

    'moment' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/moment/min/',
        'js' => [
            'moment.min.js'
        ],
    ],

    'webpack' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/bundle/',
        'js' => [
            'vendor.js',
            'dll/react-dll.js',
            'dll/utils-dll.js',
            'common.js',
            env('APP_DEV_SERVER') ? 'hot.js' : null
        ],
//        'css' => [
//            'styles/vendor.css'
//        ]
    ],

    'pubsub' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/pubsub-js/src/',
        'js' => [
            'pubsub.js'
        ],
    ],

    'font-awesome' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/font-awesome/',
        'css' => [ 'css/font-awesome.min.css' ],
    ],

    'gotham-webfont' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/gotham-fonts/',
        'css' => [ 'css/gotham-rounded.css' ],
    ],

    'roboto-fontface' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/roboto-fontface/',
        'css' => [ 'css/roboto/roboto-fontface.css' ],
    ],

    'raleway-webfont' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/raleway-webfont/',
        'css' => [ 'raleway.min.css' ],
    ],

    'yanone-kaffeesatz-webfont' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/@typopro/web-yanone-kaffeesatz/',
        'css' => [ 'TypoPRO-YanoneKaffeesatz.css' ],
    ],

    'md-icons' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/material-design-icons-iconfont/dist',
        'css' => [ 'material-design-icons.css' ],
    ],

    /**
     * Declare the ff yii dependencies as packages
     */
    'yiigridview' => [
        'baseUrl' => $ziiBasePath . '/gridview',
        'js' => [
            'jquery.yiigridview.js',
        ],
        'depends' => [ 'jquery', 'bbq' ]
    ],

    /**
     * @todo Remove this package
     */
    'daterangepicker' => [
        'baseUrl' => '/heirs/js/tools/daterangepicker',
        'css' => [
            'daterangepicker-bs2.css'
        ],
        'js' => [
            'daterangepicker.js',
        ],
        'depends' => 'moment'
    ],

    'bootstrap.css' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/styles/',
        'css' => [ 'bootstrap.css' ]
    ],

    /**
     * -------------------------------------------------------------------------
     * jQuery plugins
     * -------------------------------------------------------------------------
     */
    'autosize' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/autosize/dist/',
        'js' => [
            'autosize.min.js',
        ],
        'depends' => [ 'jquery' ],
    ],

    'background-video' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/jquery-background-video/',
        'js' => [
            'jquery.background-video.js',
        ],
        'css' => [
            'jquery.background-video.css',
        ],
        'depends' => [ 'jquery' ],
    ],

    'backstretch' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/jquery.backstretch/',
        'js' => [
            'jquery.backstretch.min.js',
        ],
        'depends' => [ 'jquery' ],
    ],

    'block-ui' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/block-ui/',
        'js' => [
            'jquery.blockUI.js',
        ],
        'depends' => [ 'jquery' ],
    ],

    'bootstrap-notify' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/bootstrap-notify/',
        'js' => [
            'jquery.bootstrap-notify.js',
        ],
        'depends' => [ 'bootstrap.js', 'jquery' ],
    ],

    'counterup' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/jquery.counterup/',
        'js' => [
            'jquery.counterup.min.js'
        ],
        'depends' => [ 'jquery', 'waypoints' ]
    ],

    'gritter' => [
        'baseUrl' => $appBaseUrl . '/',
        'js' => [
            'js/jquery.gritter.js'
        ],
        'css' => [
            'css/gritter.css'
        ],
        'depends' => [ 'jquery' ]
    ],

    'notific8' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/notific8/dist',
        'js' => [
            'jquery.notific8.min.js'
        ],
        'css' => [
            'notific8.min.css'
        ],
        'depends' => [ 'jquery' ]
    ],

    'repeater' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/jquery.repeater/',
        'js' => [
            'jquery.repeater.min.js'
        ],
        'depends' => [ 'jquery' ]
    ],

    'slimscroll' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/jquery-slimscroll/',
        'js' => [
            'jquery.slimscroll.min.js'
        ],
        'depends' => [ 'jquery' ]
    ],

    'sweetalert' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/',
        'js' => [
            'lib/bootstrap-sweetalert/dist/sweetalert.js',
            'app/global/scripts/ui/sweetalert.js',
        ],
        'css' => [ 'lib/bootstrap-sweetalert/dist/sweetalert.css' ],
        'depends' => [ 'bootstrap.js' ],
    ],

    'toastr' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/toastr/build',
        'js' => [
            'toastr.min.js'
        ],
        'css' => [
            'toastr.min.css'
        ],
        'depends' => [ 'jquery' ]
    ],

    'waypoints' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/waypoints/lib/',
        'js' => [
            'jquery.waypoints.min.js'
        ],
        'depends' => [ 'jquery' ]
    ],

    'fullcalendar' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/fullcalendar/dist/',
        'js' => [
            'fullcalendar.min.js',
        ],
        'css' => [
            'fullcalendar.min.css'
        ],
        'depends' => [ 'jquery', 'moment' ]
    ],

    'fullcalendar-scheduler' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/fullcalendar-scheduler/dist/',
        'js' => [
            'scheduler.min.js',
        ],
        'css' => [
            'scheduler.min.css'
        ],
        'depends' => [ 'fullcalendar' ]
    ],

    'raphael' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/raphael/',
        'js' => [
            'raphael.min.js'
        ],
    ],

    'morris-data' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/morris-data/',
        'js' => [
            'morris.min.js'
        ],
        'css' => [
            'morris.css'
        ],
        'depends' => [ 'jquery', 'raphael' ]
    ],

    'bootstrap-modal' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/jschr-bootstrap-modal/',
        'js' => [
            'js/bootstrap-modal.js',
            'js/bootstrap-modalmanager.js'
        ],
        'css' => [
            'css/bootstrap-modal.css',
        ],
        'depends' => [ 'jquery', 'bootstrap.js' ]
    ],

    'ladda' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/ladda/dist/',
        'js' => [
            'spin.min.js',
            'ladda.min.js',
            'ladda.jquery.min.js',
        ],
        'css' => [
            'ladda-themeless.min.css',
        ],
        'depends' => [ 'jquery' ]
    ],

    'pdfjs' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/pdfjs-dist/build/',
        'js' => [
            'pdf.min.js',
        ],
    ],

    'pulsate' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/app/global/plugins/',
        'js' => [
            'jquery.pulsate.min.js',
        ],
    ],

    /**
     * Other packages
     */
    'howler' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/howler/dist/',
        'js' => [
            'howler.min.js',
        ],
    ],
    'numeral' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/numeral/min/',
        'js' => [
            'numeral.min.js',
        ],
    ],
    'socket.io' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/lib/socket.io-client/dist/',
        'js' => [
            'socket.io.min.js',
        ],
    ],

    /**
     * Application-specific packages
     */
    'natofier' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/app/modules/natofier/',
        'js' => [
            'natofier.js'
        ],
        'depends' => [ 'babel-polyfill', 'socket.io' ]
    ],

    'natofier--notifications' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/app/modules/natofier/',
        'js' => [
            'addons/notifications-addon.js'
        ],
        'depends' => [ 'natofier' ]
    ],

    'natofier--rpc' => [
        'baseUrl' => $assetManager->getBaseUrl() . '/build/app/modules/natofier/',
        'js' => [
            'addons/rpc-addon.js'
        ],
        'depends' => [ 'natofier' ]
    ]
];

foreach ($packages as $packageName => $package) {
    if (isset($packages[ $packageName ][ 'js' ])) {
        $packages[ $packageName ][ 'js' ] = array_filter($packages[ $packageName ][ 'js' ]);
    }

    if (isset($packages[ $packageName ][ 'css' ])) {
        $packages[ $packageName ][ 'css' ] = array_filter($packages[ $packageName ][ 'css' ]);
    }
}

return $packages;