@extends('layouts.admin')
@section('title', 'База знаний')
@section('content')

<page-kb>

@endsection

@section('styles')
<style>
.btn-search {
    width: 100%;
    text-align: left;
    background: #eaebef;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #dcdee5;
    cursor:pointer;
}
.btn-search:hover {
    background: #fff;
}
.right-side {
    padding: 15px;
}
.bc {
    width: 100%;
    display: flex;
    margin-left: 2.5px;
    align-items:center;
}
.bc a {
    font-size: 12px;
    color: #ccc;
    display: block;
    margin-right: 10px;
}
.bc a:last-child {
    color: #000;
}
.bc i {
    font-size: 14px;
    color: #ccc;
    position: relative;
    margin-right: 10px;
    top: 1px;

}
.bc a:hover {
    color:#ddd;
}
.btn-add {
    position: absolute;
    bottom: 0;
    background: #28a745;
    text-align: center;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #1e7c34;
    cursor:pointer;
    padding: 5px 15px;
    color: #fff;
    margin-bottom: 20px;
}
.btn-add i {
    margin-right: 5px;
}

.book-chapter {
    padding: 15px 10px;
    cursoR:pointer;
}
.book-chapter:hover {
    background: #eee;
}
.book-chapters {
    margin: 10px -10px;
}
</style>
@endsection