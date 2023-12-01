<br>
<div style="width: 100%; margin:0 auto">
    <table class="table table-bordered">
        <thead>
            <th>Parameter</th>
            <th>Value</th>
            <th>Comments</th>
        </thead>
        <tbody>
            {{ Form::open(array('url'=>'admin/save_settings','class'=>'form-horizontal')) }}
            @foreach($settings as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td><input type="text" name="{{ $item->name }}" value="{{ $item->value }}" style="width:100%" class="form-control"></td>
                    <td style="width: 600px">{{ $item->about }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" style="text-align: right">
                    <button type="submit" class="btn btn-xs btn-primary">Save changes</button>
                </td>
            </tr>
            {{ Form::close() }}
        </tbody>
    </table>
</div>