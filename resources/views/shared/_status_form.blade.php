<form action="{{ route('statuses.store') }}" method="post">
    @include('shared._errors')
    {{ csrf_field() }}
    <label for="content"></label>
    <textarea class="form-control" name="content" id="content" rows="3"
              placeholder="聊聊新鲜事儿...">{{ old('content') }}</textarea>
    <div class="text-end">
        <button type="submit" class="btn btn-primary mt-3">发布</button>
    </div>
</form>
