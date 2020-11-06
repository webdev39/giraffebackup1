<template>
    <div class="search-container">
        <form novalidate="novalidate" onsubmit="return false;" class="search-form">
            <div role="search" class="search-wrapper">
                <input @keyup='searchKeyUp' type="search" name="search" ref="search" class="search-input" placeholder="" v-model.lazy="keywords" v-debounce="800">

                <div class="search-append">
                    <ul v-click-outside="hideResults" v-if="results.length > 0 && showResults" class="search-results">
                        <li @click="selectedAction(index)" v-for="(item, index) in results" :key="index" v-text="item['name']" :class="setHighlighted(index)"></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import debounce             from "v-debounce"
    import clickOutside         from 'v-click-outside'

    export default {
        data() {
            return {
                showResults: true,
                highlightedIndex: 0,
                keywords: '',
                results: [],
                selectedResult: {}
            };
        },
        directives: {
            debounce,
            'clickOutside': clickOutside.directive
        },
        watch: {
            keywords(after, before) {
                if (after !== before) {
                    this.fetch();
                    this.showResults = true;
                }
            }
        },
        methods: {
            searchKeyUp(e) {
                let name   = this.lowerFirst(e.code + 'Action'),
                    fnName = (this[name]);

                if (typeof fnName === 'function') {
                    fnName()
                }
            },

            lowerFirst (string) {
                return string.charAt(0).toLowerCase() + string.slice(1)
            },
            upperFirst (string) {
                return string.charAt(0).toUpperCase() + string.slice(1)
            },
            selectedAction (index) {
                this.highlightedIndex = index;
                this.setSelectedResult(index);
                setTimeout(() => { this.enterAction() }, 200);
            },
            setHighlighted (index) {
                if (this.highlightedIndex === index) {
                    return 'highlighted';
                }
            },
            setSelectedResult (index) {
                if (this.results.length > 0) {
                    this.selectedResult = this.results[index];
                }
            },
            hideResults() {
                this.showResults = false;
            },
            fetch() {
                this.results = [];

                this.$api.search.find(this.keywords).then(data => {
                    Object.entries(data.result).forEach(([key, value]) => {
                        value.forEach((e) => {
                            this.results.push({id: e.id, type: key.slice(0, -1), name: e.name});
                        });
                    });

                    this.setSelectedResult(this.highlightedIndex);
                }).catch(err => {
                    this.$notify({type:'error', text: err.response.data.message});
                });
            },
            enterAction () {
                if (this.showResults
                    && Object.keys(this.selectedResult).length > 0) {
                    this.showType(this.selectedResult.type);
                }
            },
            showType (type) {
                let name   = 'show' + this.upperFirst(type),
                    fnName = (this[name]);
                if (typeof fnName === 'function') {
                    fnName()
                }
            },
        }
    }
</script>
