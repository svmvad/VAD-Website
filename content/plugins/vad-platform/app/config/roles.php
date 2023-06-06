<?php

return [

    'vad-admin' => [
        'capabilities' => [
            'read'=>true,
            'fcrm_view_dashboard'=>true,
            'fcrm_read_contacts'=>true,
            'fluentform_dashboard_access'=>true,
            'fluentform_entries_viewer'=>true,
            'fluentform_manage_entries'=>true,
        ],
        'name'         => 'VAD Admin',
        'id'           => 'vad_admin',
    ]

];