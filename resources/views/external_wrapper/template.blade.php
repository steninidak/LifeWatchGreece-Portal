<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
        @include($view_head)
	<title>{{ $title }}</title>
</head>
<body>
        @include($view_body_top)
        {!! $content !!}
        @include($view_body_bottom)
</body>
</html>
@if(Session::has('toastr'))
    <? $toastr = Session::get('toastr') ?>
    <script type="text/javascript">
        switch('{{ $toastr[0] }}'){
            case 'info':
                toastr.info('{{ $toastr[1] }}');
                break;
            case 'success':
                toastr.success('{{ $toastr[1] }}');
                break;
            case 'warning':
                toastr.warning('{{ $toastr[1] }}');
                break;
            case 'error':
                toastr.error('{{ $toastr[1] }}');
                break;
        }
    </script>
    <? $toastr = Session::forget('toastr') ?>
@endif
