/* eslint-disable camelcase */
export const editorConfig = {
	images_upload_url: '/upload/images/',
	automatic_uploads: true,
	setup(editor) {
		editor.on('init change', () => editor.uploadImages())
	},
	paste_data_images: false,
	resize: true,
	autosave_ask_before_unload: true,
	powerpaste_allow_local_images: true,
	browser_spellcheck: true,
	contextmenu: true,
	spellchecker_whitelist: ['Ephox', 'Moxiecode'],
	language: 'ru',
	convert_urls: false,
	relative_urls: false,
	language_url: '/static/langs/ru.js',
	fontsize_formats:
		'8pt 10pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 20pt 22pt 24pt 26pt 28pt 30pt 36pt',
	lineheight_formats:
		'8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 36pt',
	plugins: [
		' advlist anchor autolink codesample colorpicker fullscreen help image imagetools ',
		' lists link media noneditable preview',
		' searchreplace table template textcolor visualblocks wordcount code',
	],
	menubar: false, //'file edit view insert format tools table help',
	toolbar: [
		'styleselect',
		'bold italic underline strikethrough',
		'table',
		'fontselect fontsizeselect formatselect',
		'alignleft aligncenter alignright alignjustify',
		'outdent indent',
		'numlist bullist',
		'forecolor backcolor removeformat',
		'fullscreen preview',
		'media link',
		'undo redo',
	].join(' | '),
	toolbar_sticky: true,
	content_style:
		'.lineheight20px { line-height: 20px; }' +
		'.lineheight22px { line-height: 22px; }' +
		'.lineheight24px { line-height: 24px; }' +
		'.lineheight26px { line-height: 26px; }' +
		'.lineheight28px { line-height: 28px; }' +
		'.lineheight30px { line-height: 30px; }' +
		'.lineheight32px { line-height: 32px; }' +
		'.lineheight34px { line-height: 34px; }' +
		'.lineheight36px { line-height: 36px; }' +
		'.lineheight38px { line-height: 38px; }' +
		'.lineheight40px { line-height: 40px; }' +
		'body { padding: 20px;max-width: 960px;margin: 0 auto; }' +
		'.tablerow1 { background-color: #D3D3D3; }',
	formats: {
		lineheight20px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight20px',
		},
		lineheight22px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight22px',
		},
		lineheight24px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight24px',
		},
		lineheight26px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight26px',
		},
		lineheight28px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight20px',
		},
		lineheight30px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight30px',
		},
		lineheight32px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight32px',
		},
		lineheight34px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight34px',
		},
		lineheight36px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight36px',
		},
		lineheight38px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight38px',
		},
		lineheight40px: {
			selector:
				'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
			classes: 'lineheight40px',
		},
	},
	style_formats: [
		{ title: 'lineheight20px', format: 'lineheight20px' },
		{ title: 'lineheight22px', format: 'lineheight22px' },
		{ title: 'lineheight24px', format: 'lineheight24px' },
		{ title: 'lineheight26px', format: 'lineheight26px' },
		{ title: 'lineheight28px', format: 'lineheight28px' },
		{ title: 'lineheight30px', format: 'lineheight30px' },
		{ title: 'lineheight32px', format: 'lineheight32px' },
		{ title: 'lineheight34px', format: 'lineheight34px' },
		{ title: 'lineheight36px', format: 'lineheight36px' },
		{ title: 'lineheight38px', format: 'lineheight38px' },
		{ title: 'lineheight40px', format: 'lineheight40px' },
	],
	content_css: [
		'//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
		'/static/css/mycontent.css',
	],
}
