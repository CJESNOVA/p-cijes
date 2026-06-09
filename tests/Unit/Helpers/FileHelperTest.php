<?php

use App\Helpers\FileHelper;

it('sanitizes a normal filename', function () {
    $result = FileHelper::sanitizeFileName('My Document.pdf');
    expect($result)->toBe('my_document.pdf');
});

it('strips accented characters', function () {
    $result = FileHelper::sanitizeFileName('café résumé.docx');
    expect($result)->toBe('cafe_resume.docx');
});

it('replaces special characters with underscores', function () {
    $result = FileHelper::sanitizeFileName('file@name#2024!.txt');
    expect($result)->toBe('file_name_2024.txt');
});

it('collapses multiple underscores', function () {
    $result = FileHelper::sanitizeFileName('a___b___c.png');
    expect($result)->toBe('a_b_c.png');
});

it('trims leading and trailing underscores from the name', function () {
    $result = FileHelper::sanitizeFileName('___hello___.jpg');
    expect($result)->toBe('hello.jpg');
});

it('handles a filename with no extension', function () {
    $result = FileHelper::sanitizeFileName('noext');
    expect($result)->toBe('noext.');
});

it('preserves valid alphanumeric filenames', function () {
    $result = FileHelper::sanitizeFileName('valid-file_123.csv');
    expect($result)->toBe('valid-file_123.csv');
});

it('handles unicode characters', function () {
    $result = FileHelper::sanitizeFileName('日本語ファイル.pdf');
    expect($result)->toBe('.pdf');
});

it('handles spaces in filename', function () {
    $result = FileHelper::sanitizeFileName('my file name.txt');
    expect($result)->toBe('my_file_name.txt');
});
