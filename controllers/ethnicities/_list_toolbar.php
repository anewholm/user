<div data-control="toolbar">
    <a
        href="<?= Backend::url('acorn/user/ethnicities/create') ?>"
        class="btn btn-primary wn-icon-plus">
        <?= e(trans('backend::lang.form.create_title', ['name' => trans('acorn.user::lang.models.ethnicity.label')])); ?>
    </a>

    <button
        class="btn btn-danger wn-icon-trash-o"
        disabled="disabled"
        onclick="$(this).data('request-data', { checked: $('.control-list').listWidget('getChecked') })"
        data-request="onDelete"
        data-request-confirm="<?= e(trans('backend::lang.list.delete_selected_confirm')); ?>"
        data-trigger-action="enable"
        data-trigger=".control-list input[type=checkbox]"
        data-trigger-condition="checked"
        data-request-success="$(this).prop('disabled', 'disabled')"
        data-stripe-load-indicator>
        <?= e(trans('backend::lang.list.delete_selected')); ?>
    </button>
</div>
