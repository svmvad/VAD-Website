<?php

namespace YOOtheme;

$items = $props['items'];

$el = $this->el('nav', [
    'aria-label' => 'Breadcrumb',
]);

$list = $this->el('ul', [
    'class' => [
        'uk-breadcrumb uk-margin-remove-bottom',
        'uk-flex-{text_align}[@{text_align_breakpoint} [uk-flex-{text_align_fallback}]]',
    ],
    'vocab' => 'https://schema.org/',
    'typeof' => 'BreadcrumbList',
]);

$li = $this->el('li', [
    'property' => 'itemListElement',
    'typeof' => 'ListItem',
]);

$span = $this->el('span', ['property' => 'name']);

?>

<?php if ($items) : ?>

    <?= $el($props, $attrs) ?>

    <?= $list($props) ?>

    <?php foreach ($items as $key => $item) : ?>

        <?php if (!empty($item->link)) : ?>
            <?= $li() ?>
            <a href="<?= $item->link ?>" property="item" typeof="WebPage"><?= $span([], $item->name) ?></a>
        <?php elseif ($key !== array_key_last($items)) : ?>
            <?= $li(['class' => 'uk-disabled']) ?>
            <a><?= $span([], $item->name) ?></a>
        <?php else : ?>
            <?= $li() ?>
            <?= $span([], $item->name) ?>
        <?php endif ?>
        <meta property="position" content="<?= $key ?>">
        <?= $li->end() ?>
    <?php endforeach ?>

    <?= $list->end() ?>

    <?= $el->end() ?>

<?php endif ?>
