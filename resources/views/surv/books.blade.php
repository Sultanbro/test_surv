@extends('layouts.admin')
@section('title', 'Обучение')
@section('content')
    <div class="container">
        <div class="card p-3" id="card1">
            <h3>Обучение</h3>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://cdn.tiny.cloud/1//tinymce/5/tinymce.min.js">
</script>
<script>
    var demoBaseConfig = {
        selector: '#big_text',
        height: 500,
        resize: true,
        autosave_ask_before_unload: false,
        powerpaste_allow_local_images: true,
        plugins: [
            " lists advlist anchor autolink codesample colorpicker fullscreen help image imagetools tinydrive",
            " lists link media noneditable  preview",
            " searchreplace table template textcolor  visualblocks wordcount "
        ],

        toolbar: "numlist bullist insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive",
        content_css: [
            "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
            "//www.tiny.cloud/css/content-standard.min.css"
        ],
        spellchecker_dialog: true,
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],
        tinydrive_demo_files_url: '/docs/demo/tiny-drive-demo/demo_files.json',
        tinydrive_token_provider: function(success, failure) {
            success({
                token: ''
            });
        }
    };
    var demoBaseConfig2 = {
        selector: '#small_text',
        height: 500,
        resize: true,
        autosave_ask_before_unload: false,
        powerpaste_allow_local_images: true,
        plugins: [
            " lists advlist anchor autolink codesample colorpicker fullscreen help image imagetools tinydrive",
            " lists link media noneditable  preview",
            " searchreplace table template textcolor  visualblocks wordcount "
        ],

        toolbar: "numlist bullist insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive",
        content_css: [
            "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
            "//www.tiny.cloud/css/content-standard.min.css"
        ],
        spellchecker_dialog: true,
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],
        tinydrive_demo_files_url: '/docs/demo/tiny-drive-demo/demo_files.json',
        tinydrive_token_provider: function(success, failure) {
            success({
                token: ''
            });
        }
    };
    var demoBaseConfig3 = {
        selector: '#big_text2',
        height: 500,
        resize: true,
        autosave_ask_before_unload: false,
        powerpaste_allow_local_images: true,
        plugins: [
            " lists advlist anchor autolink codesample colorpicker fullscreen help image imagetools tinydrive",
            " lists link media noneditable  preview",
            " searchreplace table template textcolor  visualblocks wordcount "
        ],

        toolbar: "numlist bullist insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive",
        content_css: [
            "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
            "//www.tiny.cloud/css/content-standard.min.css"
        ],
        spellchecker_dialog: true,
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],
        tinydrive_demo_files_url: '/docs/demo/tiny-drive-demo/demo_files.json',
        tinydrive_token_provider: function(success, failure) {
            success({
                token: ''
            });
        }
    };
    var demoBaseConfig4 = {
        selector: '#small_text2',
        height: 500,
        resize: true,
        autosave_ask_before_unload: false,
        powerpaste_allow_local_images: true,
        plugins: [
            " lists advlist anchor autolink codesample colorpicker fullscreen help image imagetools tinydrive",
            " lists link media noneditable  preview",
            " searchreplace table template textcolor  visualblocks wordcount "
        ],

        toolbar: "numlist bullist insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive",
        content_css: [
            "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
            "//www.tiny.cloud/css/content-standard.min.css"
        ],
        spellchecker_dialog: true,
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],
        tinydrive_demo_files_url: '/docs/demo/tiny-drive-demo/demo_files.json',
        tinydrive_token_provider: function(success, failure) {
            success({
                token: ''
            });
        }
    };
    var demoBaseConfig5 = {
        selector: '#small_text3',
        height: 500,
        resize: true,
        autosave_ask_before_unload: false,
        powerpaste_allow_local_images: true,
        plugins: [
            "lists  advlist anchor autolink codesample colorpicker fullscreen help image imagetools tinydrive",
            " lists link media noneditable  preview",
            " searchreplace table template textcolor  visualblocks wordcount "
        ],

        toolbar: "numlist bullist insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive",
        content_css: [
            "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
            "//www.tiny.cloud/css/content-standard.min.css"
        ],
        spellchecker_dialog: true,
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],
        tinydrive_demo_files_url: '/docs/demo/tiny-drive-demo/demo_files.json',
        tinydrive_token_provider: function(success, failure) {
            success({
                token: ''
            });
        }
    };
    var demoBaseConfig6 = {
        selector: '#big_text3',
        height: 500,
        resize: true,
        autosave_ask_before_unload: false,
        powerpaste_allow_local_images: true,
        plugins: [
            "  advlist anchor autolink codesample colorpicker fullscreen help image imagetools tinydrive",
            " lists link media noneditable  preview",
            " searchreplace table template textcolor  visualblocks wordcount "
        ],

        toolbar: "numlist bullist insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive",
        content_css: [
            "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
            "//www.tiny.cloud/css/content-standard.min.css"
        ],
        spellchecker_dialog: true,
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],
        tinydrive_demo_files_url: '/docs/demo/tiny-drive-demo/demo_files.json',
        tinydrive_token_provider: function(success, failure) {
            success({
                token: ''
            });
        }
    };

    var demoBaseConfig7 = {
        selector: '#big_text4',
        height: 200,
        resize: true,
        autosave_ask_before_unload: false,
        powerpaste_allow_local_images: true,
        plugins: [
            "  advlist anchor autolink codesample colorpicker fullscreen help image imagetools tinydrive",
            " lists link media noneditable  preview",
            " searchreplace table template textcolor  visualblocks wordcount "
        ],

        toolbar: "numlist bullist insertfile a11ycheck undo redo | bold italic | forecolor backcolor | template codesample | alignleft aligncenter alignright alignjustify | bullist numlist | link image tinydrive",
        content_css: [
            "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
            "//www.tiny.cloud/css/content-standard.min.css"
        ],
        spellchecker_dialog: true,
        spellchecker_whitelist: ['Ephox', 'Moxiecode'],
        tinydrive_demo_files_url: '/docs/demo/tiny-drive-demo/demo_files.json',
        tinydrive_token_provider: function(success, failure) {
            success({
                token: ''
            });
        }
    };

    tinymce.init(demoBaseConfig2);
    tinymce.init(demoBaseConfig);
    tinymce.init(demoBaseConfig7);
    tinymce.init(demoBaseConfig3);
    tinymce.init(demoBaseConfig4);
    tinymce.init(demoBaseConfig5);
    tinymce.init(demoBaseConfig6);
</script>
@endsection
