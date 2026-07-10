<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label required" for="name">عنوان دسته‌بندی</label>
        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name ?? '') }}">
        <x-field-error name="name" />
    </div>

    <div class="col-md-4 d-flex align-items-end pb-2">
        <div>
            <input type="hidden" name="is_active" value="0">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked((bool) old('is_active', $category->is_active ?? true))>
                <label class="form-check-label" for="is_active">دسته فعال باشد</label>
            </div>
            <x-field-error name="is_active" />
        </div>
    </div>

    <div class="col-md-8">
        <label class="form-label" for="image">تصویر دسته‌بندی</label>
        <input id="image" type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
        <div class="form-text">فرمت‌های مجاز: jpg، jpeg، png، webp. حداکثر حجم: ۲ مگابایت. تصویر هنگام ذخیره بهینه و resize می‌شود.</div>
        <x-field-error name="image" />
    </div>

    @if(! empty($category?->image))
        <div class="col-md-4">
            <label class="form-label d-block">تصویر فعلی</label>
            <img class="thumb" src="{{ '/storage/'.ltrim($category->image, '/') }}" alt="{{ $category->name }}">
        </div>
    @endif
</div>

<div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary">ذخیره</button>
    <a class="btn btn-light" href="{{ route('categories.index') }}">انصراف</a>
</div>
