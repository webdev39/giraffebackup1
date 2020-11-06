export default {
    data() {
        return {
            emoji: [],
        }
    },
    methods: {
        /**
         * Replace text to tags
         *
         * @param {string} string
         */
        stringToHTML(string) {
            if (string) {
                string = String(string);
                string = string.replace(/([^\S]|^)(((https?\:\/\/)|(www\.))(\S+\.)(\S+))/gi, function(match, space, url) {
                    let hyperlink = url;
                    let separator = '';

                    if (!hyperlink.match('^https?:\/\/')) {
                        hyperlink = 'http://' + hyperlink;
                    }

                    let hyperlinkLength = hyperlink.length;

                    if (hyperlink[hyperlinkLength - 1].match(/[,.;?!]/gi)) {

                        separator = hyperlink[hyperlinkLength - 1];
                        url = url.substring(0, url.length - 1);
                    }

                    return space + '<a href="' + hyperlink + '" target="_blank">' + url + '</a>' + separator;
                });
                string = string.replace(/(?:\r\n|\r|\n)/gi, "<br>");

                let that = this;

                return string = string.replace(/(@(\S+))/gi, function(match, space, url) {
                    let tag = that.getInsertHTMLTag(match);

                    return tag || match;
                });

                // let words = string.split(" ").map((word) => {
                //     let tag = this.getInsertHTMLTag(word);
                //
                //     return tag || word;
                // });
                //
                // words = words.join('&nbsp');

            }

            return '';
        },

        /**
         * Get html tag for insertion
         *
         * @return {object|null}
         */
        getInsertHTMLTag(text) {
            if (/^@(.*)/gi.test(text)) {
               let user = this.findUser(text);

                if (this.lastChangeWord && this.lastChangeWord.container) {
                    let parent = this.lastChangeWord.container.parentNode;

                    if (parent.className === 'user') {
                        return user ? null : this.lastChangeWord.text;
                    }
                }

                return this.getUserTag(user);
            }

            return this.getEmojiTag(text);
        },
        /**
         * Deleting @ from the suggested user name
         *
         * @param {string} string
         */
        getUserName(string) {
            return String(string).trim().replace(/^@/g, '');
        },
        /**
         * Search for a user by name
         *
         * @param {string} username
         */
        findUser(username) {
            username = this.getUserName(username);

            return this.$lodash.find(this.users, (user) => {
                return String(user.name).toLowerCase() === username.toLowerCase();
            });
        },
        /**
         * Search emoji
         *
         * @param {string} emoji
         */
        findEmoji(emoji) {
            return this.$lodash.find(this.emoji, {symbol: emoji});
        },
        /**
         * Getting a tag for user
         *
         * @param {object|string} user
         */
        getUserTag(user) {
            if (typeof user === 'string') {
                user = this.findUser(user);
            }

            if (user) {
                return `<span class="user">@${user.name}</span>`
            }

            return null;
        },
        /**
         * Getting a tag for emoji
         *
         * @param {object|string} emoji
         */
        getEmojiTag(emoji) {
            if (typeof emoji === 'string') {
                emoji = this.findEmoji(emoji);
            }

            if (emoji) {
                return `<img class="emoji" src="${emoji.img}" alt="${emoji.symbol}" />`
            }

            return null;
        },
    }
};
