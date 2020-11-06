<template>
    <div class="dropdown" id="lang-btn">
        <a
            href="#"
            class="dropdown-toggle"
            data-toggle="dropdown"
            role="button"
            aria-haspopup="true"
            aria-expanded="false"
        >
            {{ getLanguage }}
        </a>

        <ul class="dropdown-menu">
            <li
                v-for="(language, index) in languages"
                :key="index"
            >
                <a href="#" @click.prevent="setLanguage(language.iso_639_1)">{{ language.iso_639_1 }}</a>
            </li>
        </ul>
    </div>
</template>

<script>
	import {mapGetters} from 'vuex'

	export default {
		data () {
			return {
				language: 'en'
			}
		},
		computed: {
			...mapGetters({
				languages:      'default/getLocalLanguages',
				getUserProfile: 'user/getUserProfile',
			}),
			getLanguage() {
				return this.language;
			}
		},
		methods: {
			setLanguage(lan){
				this.language = lan;
				this.$i18n.locale = lan;
			}
		}
	}
</script>

<style lang="scss">
    #lang-btn {
        position: absolute;
        top: 8px;
        right: 6px;
        width: 25px;
        height: 25px;
        background-color: white;
        padding: 3px;
        font-size: 13px;

        a {
            font-family: inherit;
            text-decoration: none;
            text-transform: uppercase;
            color: inherit;
        }

        .dropdown-menu {
            min-width: 5rem;
        }

        .dropdown-toggle::after {
            display: none;
        }
    }
</style>
