import Vue                  from 'vue'
import VueQuillEditor       from 'vue-quill-editor'

import Quill                from 'quill'
import MagicUrl             from 'quill-magic-url'
import BlotFormatter        from "quill-blot-formatter";

import QuillEmoji from 'quill-emoji';

const { EmojiBlot, ShortNameEmoji, ToolbarEmoji, TextAreaEmoji } = QuillEmoji;

// import EmojiBlot            from '@vendor/quill-emoji/src/format-emoji-blot';
// import ShortNameEmoji       from '@vendor/quill-emoji/src/module-emoji';
// import ToolbarEmoji         from '@vendor/quill-emoji/src/module-toolbar-emoji';
// import TextAreaEmoji        from '@vendor/quill-emoji/src/module-textarea-emoji';

import 'quill-drag-and-drop-module';
import 'quill-mention';

import '@vendor/quill-emoji/dist/quill-emoji.css';
import '@vendor/quill-mention/dist/quill.mention.min.css';

Quill.register({
    'formats/emoji': EmojiBlot,
    'modules/emoji-shortname': ShortNameEmoji,
    'modules/emoji-toolbar': ToolbarEmoji,
    'modules/emoji-textarea': TextAreaEmoji,

    'modules/blotFormatter': BlotFormatter,
    'modules/magicUrl': MagicUrl,
}, true);

let Video = Quill.import('formats/video');

Video.sanitize = function(url){
    let match = url.match(/^(?:(https?):\/\/)?(?:(?:www|m)\.)?youtube\.com\/watch.*v=([a-zA-Z0-9_-]+)/) || url.match(/^(?:(https?):\/\/)?(?:(?:www|m)\.)?youtu\.be\/([a-zA-Z0-9_-]+)/);
    if (match) {
        return (match[1] || 'https') + '://www.youtube.com/embed/' + match[2] + '?showinfo=0';
    }
    if (match = url.match(/^(?:(https?):\/\/)?(?:www\.)?vimeo\.com\/(\d+)/)) {
        // eslint-disable-line no-cond-assign
        return (match[1] || 'https') + '://player.vimeo.com/video/' + match[2] + '/';
    }

    if (match = url.match(/^(?:(https?):\/\/)?(?:www\.)?loom\.com\/share\/([a-z0-9]*)/)){
        return (match[1] || 'https') + '://www.loom.com/embed/' + match[2];
    }

    return '/video/wrong-url?message='+window.app.$i18n.t('wrong_video_url');
};

Vue.use(VueQuillEditor);