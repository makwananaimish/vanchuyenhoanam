<div class="row mb-6 cost-row" data-index="@isset($index){{$index}}@endisset" >
    <div class="col-2">
        <input type="text" class="form-control" name="{{ $inputName }}[ordinal]" value="{{ $cost['ordinal'] }}">
    </div>
    <div class="col-4">
        <input type="text" class="form-control" name="{{ $inputName }}[content]" value="{{ $cost['content'] }}">
    </div>
    <div class="col-3">
        <input type="text" class="form-control input-currency" name="{{ $inputName }}[amount]"
            value="{{ $cost['amount'] }}">
    </div>
    <div class="col-3">
        <input type="text" class="form-control" name="{{ $inputName }}[note]" value="{{ $cost['note'] }}">
    </div>
</div>
