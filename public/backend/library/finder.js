(function ($) {
    "use strict";
    var HT = {};

    HT.setupCkeditor = () => {
        if ($('.ck-editor')) {
            $('.ck-editor').each(function () {
                let editor = $(this)
                let elemetId = editor.attr('id')
                let elementHeight = editor.attr('data-height')
                HT.ckeditor4(elemetId, elementHeight)
            })
        }
    }
    HT.multipleUploadImageCkeditor = () => {
        $(document).on('click', '.multipleUploadImageCkeditor', function (e) {
            let object = $(this)
            let target = object.attr('data-target')
            HT.BrownServerCkeditor(object, 'Images', target);
            e.preventDefault()
        })
    }

    HT.ckeditor4 = (elementId, elementHeight) => {
        if (typeof (elementHeight) == 'undefined') {
            elementHeight = 500;
        }
        CKEDITOR.replace(elementId, {
            height: elementHeight,
            removeButtons: '',
            entities: true,
            allowedContent: true,
            toolbarGroups: [
                { name: 'clipboard', groups: ['clipboard', 'undo'] },
                { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'undo'] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'document', groups: ['mode', 'document', 'doctools'] },
                { name: 'color' },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: ['basicstyles', 'cleanup', 'colors', 'styles', 'indent'] },
                { name: 'paragraph', groups: ['list', '', 'blocks', 'align', 'bidi'] },
                { name: 'styles' }
            ],
            // removeButtons: 'Save,NewPage,Pdf,Preview,Print,Find,Replace,CreateDiv,SelectAll,Symbol,Block,Button,Language',
            // removePlugins: "exportpdf",
        });
    }
    HT.uploadImageAvatar = () => {
        $('.image-target').click(function () {
            let input = $(this)
            let type = 'Images';
            HT.browServerAvatar(input, type);
        })
    }
    HT.browServerAvatar = (object, type) => {
        if (typeof (type) == 'undefined') {
            type = 'Images';
        }
        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function (fileUrl, data) {
            // Loại bỏ /laravelversion1.com/public khỏi fileUrl
            object.find('img').attr('src', fileUrl)
            object.siblings('input').val(fileUrl) // Gán URL đã được loại bỏ chuỗi
        };
        finder.popup();
    }
    HT.BrownServerCkeditor = (object, type, target) => {
        if (typeof (type) == 'undefined') {
            type = 'Images';
        }
        var finder = new CKFinder();

        finder.resourceType = type;
        finder.selectActionFunction = function (fileUrl, data, allFiles) {
            let html = '';
            for (var i = 0; i < allFiles.length; i++) {
                var image = allFiles[i].url
                html += '<div class="image-content"><figure>'
                html += '<img src="' + image + '" alt="' + image + '">'
                html += '<figcaption>Nhập vào mô tả cho ảnh</figcaption>'
                html += '</figure></div>';
            }
            CKEDITOR.instances[target].insertHtml(html)
        }
        finder.popup();
    }

    HT.uploadImageToInput = () => {
        $('.upload-image').click(function () {
            let input = $(this)
            let type = input.attr('data-type')
            HT.setupCkFinder2(input, type);
        })
    }
    HT.setupCkFinder2 = (object, type) => {
        if (typeof (type) == 'undefined') {
            type = 'Images';
        }
        var finder = new CKFinder();
        finder.resourceType = type;
        finder.selectActionFunction = function (fileUrl, data) {
            // Loại bỏ /laravelversion1.com/public khỏi fileUrl
            var cleanFileUrl = fileUrl.replace('/laravelversion1.com/public', '');
            object.val(cleanFileUrl); // Gán URL đã được loại bỏ chuỗi
        };
        finder.popup();
    };

    $(document).ready(function () {
        HT.uploadImageToInput();
        HT.setupCkeditor();
        HT.uploadImageAvatar();
        HT.multipleUploadImageCkeditor();
    });

})(jQuery);