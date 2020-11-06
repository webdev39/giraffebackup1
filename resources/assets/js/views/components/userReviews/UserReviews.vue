<template>

    <div class="review-block">

        <div
            v-if="!showBlock"
            class="review-block-header"
            @click="showBlock = true"
        >Send review</div>

        <form
            v-if="showBlock"
            class="form-rating-holder"
            @submit.prevent="handleSend"
        >

            <div class="form-rating-holder-title">{{ $t('reviews_title') }}</div>

            <div class="form-rating-holder-star">

                <star-rating
                    v-model="form.rating"
                    :show-rating="false"
                    :star-size="20"
                    :border-width="4"
                    :rounded-corners="true"
                    :glow="1"
                    :star-points="[23,2, 14,17, 0,19, 10,34, 7,50, 23,43, 38,50, 36,34, 46,19, 31,17]"
                    border-color="#d8d8d8"
                ></star-rating>

            </div>

            <div class="form-rating-holder">
                <div class="form-rating-row">
                    <textarea
                        v-model="form.comment"
                        id="comment"
                        rows="3"
                        class="form-rating-textarea"
                    ></textarea>
                </div>
                <div class="form-rating-row-center">
                    <theme-button-success
                        type="submit"
                        class="btn btn-update"
                    >
                        {{ $t('save') }}
                    </theme-button-success>
                </div>
            </div>

        </form>

        <button
            v-if="showBlock"
            type="button"
            title="Close modal task detail"
            class="btn control-btns-hide review-block-close"
            @click="showBlock = false"
        >
            <i class="icon-close"><svg xmlns="http://www.w3.org/2000/svg" class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#icon-close"></use></svg></i>
        </button>
    </div>

</template>

<script>
	import StarRating from 'vue-star-rating';
	import Validation from "@views/components/Validation";
	import ThemeButtonSuccess from "@views/layouts/theme/buttons/ThemeButtonSuccess";

	export default {
		name: "UserReviews",
        components: {
			StarRating,
			ThemeButtonSuccess,
			Validation
        },
        props: {
		    user: {
		    	type: Object,
                default: () => {},
            }
        },
        data() {
			return {
				form: {
					rating: null,
                    comment: '',
				},
				showBlock: false
			}
        },
        methods: {

			handleSend() {
				// this.$api.review.create(this.form)
                //     .then(response => {
				// 		// this.$notify({type:'success', text: this.$t('review_send')});
                //     });
            }
		}
	}
</script>

<style scoped lang="scss">
    .review-block {
        position: fixed;
        top: 50%;
        right: 0;
        padding: 20px;
        max-width: 300px;
        min-height: 115px;
        border: 2px solid #376aa7;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        background-color: #fff;
        transform: translateY(-50%);
        box-shadow: 0 0 70px -6px rgba(0, 0, 0, 0.3);
        transition: all 0.3s;
        z-index: 900;

        &-header {
            position: absolute;
            transform: rotate(-90deg);
            top: 50%;
            left: -20px;
            margin-top: -10px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: color 0.3s;

            &:hover {
                color: #376aa7;
            }
        }

        &-close {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: transparent;
            border-radius: 0;
            box-shadow: none;

            svg {
                width: 15px;
                height: 15px;
            }
        }
    }

    .form-rating {

        &-row {
            &-center {
                text-align: center;
            }
        }

        &-holder {
            &-title {
                margin-bottom: 5px;
                font-size: 16px;
                font-weight: 600;
                text-align: center;
            }

            &-star {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 20px;
            }

            .form-rating-textarea {
                width: 100%;
                color: #383838;
                min-height: 36px;
                font-size: 16px;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
                border-color: #f3f3f3;
                padding: 10px;
                resize: none;
            }
        }
    }
</style>