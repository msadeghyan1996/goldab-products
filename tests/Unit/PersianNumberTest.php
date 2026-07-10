<?php

use App\Support\PersianNumber;

test('it converts latin digits to Persian digits', function () {
    expect(PersianNumber::convert('12,345.67'))->toBe('۱۲,۳۴۵.۶۷');
});
