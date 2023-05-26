<?php

namespace YOOtheme\Builder\Templates;

use YOOtheme\Builder;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\Storage;

class TemplateController
{
    public static function index(
        Request $request,
        Response $response,
        Storage $storage,
        Builder $builder
    ) {
        $library = array_filter(
            array_map(function ($template) use ($builder) {
                if (isset($template['layout'])) {
                    $template['layout'] = $builder->load(json_encode($template['layout']));
                }

                return $template;
            }, $storage('templates', []))
        );

        return $response->withJson($library);
    }
    public static function saveTemplate(
        Request $request,
        Response $response,
        Storage $storage,
        Builder $builder
    ) {
        // Can't name 'tpl' request param 'template' because of conflict when PECL extension "json_post" is enabled
        $request
            ->abortIf(!($id = $request->getParam('id')), 400)
            ->abortIf(!($tpl = $request->getParam('tpl')), 400);

        if (isset($tpl['layout'])) {
            $tpl['layout'] = $builder
                ->withParams(['context' => 'save'])
                ->load(json_encode($tpl['layout']));
        }

        $storage->set("templates.{$id}", $tpl);

        return $response->withJson(['message' => 'success']);
    }

    public static function deleteTemplate(Request $request, Response $response, Storage $storage)
    {
        $request->abortIf(!($id = $request->getParam('id')), 400);

        $storage->del("templates.{$id}");

        return $response->withJson(['message' => 'success']);
    }

    public static function reorderTemplates(Request $request, Response $response, Storage $storage)
    {
        $request->abortIf(!($sorting = $request->getParam('templates')), 400);
        $templates = $storage->get('templates');

        $storage->set(
            'templates',
            array_merge(array_intersect_key(array_flip($sorting), $templates), $templates)
        );

        return $response->withJson(['message' => 'success']);
    }
}
