{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main" id="articleContent">
    {$object.content}
    </section>
</div>
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}