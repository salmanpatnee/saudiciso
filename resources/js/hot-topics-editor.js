import {
    ClassicEditor,
    Essentials,
    Paragraph,
    Heading,
    Bold,
    Italic,
    Link,
    List,
    BlockQuote,
    SourceEditing,
    Table,
    TableToolbar,
    GeneralHtmlSupport,
} from 'ckeditor5';
import 'ckeditor5/ckeditor5.css';

const editorSelectors = [
    '#body',
    '#description',
];

editorSelectors.forEach(selector => {
    const element = document.querySelector(selector);
    if (!element) {
        return;
    }

    ClassicEditor
        .create(element, {
            licenseKey: 'GPL',
            plugins: [
                Essentials,
                Paragraph,
                Heading,
                Bold,
                Italic,
                Link,
                List,
                BlockQuote,
                SourceEditing,
                Table,
                TableToolbar,
                GeneralHtmlSupport,
            ],
            toolbar: [
                'undo', 'redo', '|',
                'heading', '|',
                'bold', 'italic', '|',
                'link', 'blockQuote', 'insertTable', '|',
                'bulletedList', 'numberedList', '|',
                'sourceEditing',
            ],
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells',
                ],
            },
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true,
                    },
                ],
            },
        })
        .catch(error => {
            console.error(`Error initializing editor for ${selector}:`, error);
        });
});
