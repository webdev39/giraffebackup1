<template>
    <theme-manage-container class="system-settings-bill-col">

        <form
            id="bill-setting"
            class="manage-form"
            @submit.prevent="updateBillSetting"
        >

            <h1 class="manage-title">{{ $t('bill_settings') }}</h1>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="creator" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("creator") }}</strong>
                    </label>

                    <validation
                        :validator="validator"
                        label="creator"
                        class="col-xs-12 col-sm-9"
                    >
                        <input
                            v-model="form.creator"
                            id="creator"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="author" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("author") }}</strong>
                    </label>

                    <validation
                        :validator="validator"
                        label="author"
                        class="col-xs-12 col-sm-9"
                    >
                        <input
                            v-model="form.author"
                            id="author"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="title" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("title") }}</strong>
                    </label>

                    <validation
                        :validator="validator"
                        label="title"
                        class="col-xs-12 col-sm-9"
                    >
                        <input
                            v-model="form.title"
                            id="title"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="subject" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("subject") }}</strong>
                    </label>

                    <validation
                        :validator="validator"
                        label="subject"
                        class="col-xs-12 col-sm-9"
                    >
                        <input
                            v-model="form.subject"
                            id="subject"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="keywords" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("keywords") }}</strong>
                    </label>

                    <validation
                        class="col-xs-12 col-sm-9"
                        :validator="validator"
                        label="keywords"
                    >
                        <input
                            v-model="form.keywords"
                            id="keywords"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <div class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("logo") }}</strong>
                    </div>

                    <validation
                        :validator="validator"
                        label="logo"
                        class="col-xs-12 col-sm-9"
                    >
                        <button-file
                            :accept="['image/*']"
                            @change="uploadFile($event)"
                            ref="logo"
                            name="logo"
                        />

                        <div class="bill-settings-logo">
                            <div class="bill-settings-logo-holder">
                                <img :src="form.logo" alt="logo" style="max-height: 150px;">
                            </div>

                            <div class="bill-settings-logo-row">
                                <label for="logo_position">
                                    <strong>{{ $t("logo_position") }}</strong>
                                </label>

                                <validation
                                    class="col-xs-12 col-sm-9"
                                    :validator="validator"
                                    label="logo_position"
                                >
                                    <input
                                        v-model="form.bill_settings.logo_position"
                                        id="logo_position"
                                        type="text"
                                        class="form-control"
                                    >
                                </validation>
                            </div>

                            <div class="bill-settings-logo-row">
                                <label for="logo_height">
                                    <strong>{{ $t("logo_height") }}</strong>
                                </label>

                                <validation
                                    class="col-xs-12 col-sm-9"
                                    :validator="validator"
                                    label="logo_height"
                                >
                                    <input
                                        v-model="form.bill_settings.logo_height"
                                        id="logo_height"
                                        type="text"
                                        class="form-control"
                                    >
                                </validation>
                            </div>
                        </div>
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="telephone" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("telephone") }}</strong>
                    </label>

                    <validation
                        class="col-xs-12 col-sm-9"
                        :validator="validator"
                        label="telephone"
                    >
                        <input
                            v-model="form.phone"
                            id="telephone"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="email" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("email") }}</strong>
                    </label>

                    <validation
                        class="col-xs-12 col-sm-9"
                        :validator="validator"
                        label="email"
                    >
                        <input
                            v-model="form.email"
                            id="email"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="web" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("web") }}</strong>
                    </label>

                    <validation
                        class="col-xs-12 col-sm-9"
                        :validator="validator"
                        label="web"
                    >
                        <input
                            v-model="form.web"
                            id="web"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="postcode" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("postcode") }}</strong>
                    </label>

                    <validation
                        class="col-xs-12 col-sm-9"
                        :validator="validator"
                        label="postcode"
                    >
                        <input
                            v-model="form.postcode"
                            id="postcode"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="street" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("street") }}</strong>
                    </label>

                    <validation
                        class="col-xs-12 col-sm-9"
                        :validator="validator"
                        label="street"
                    >
                        <input
                            v-model="form.street"
                            id="street"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="city" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("city") }}</strong>
                    </label>

                    <validation
                        class="col-xs-12 col-sm-9"
                        :validator="validator"
                        label="city"
                    >
                        <input
                            v-model="form.city"
                            id="city"
                            type="text"
                            class="form-control"
                        >
                    </validation>
                </div>
            </div>

            <hr>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <div class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("font_family") }}</strong>
                    </div>

                    <validation
                        :validator="validator"
                        label="font_id"
                        class="col-xs-12 col-sm-9"
                    >
                        <multiselect
                            v-model="fontList"
                            track-by="id"
                            label="name"
                            :options="getFonts"
                            :allow-empty="false"
                            :searchable="true"
                            @select="selectFont"
                        />
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="fee" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("fee") }}</strong>
                    </label>

                    <validation
                        :validator="validator"
                        label="fee"
                        class="col-xs-12 col-sm-9"
                    >
                        <div class="input-group">
                            <input
                                v-model="form.fee"
                                id="fee"
                                type="number"
                                class="form-control"
                            >
                            <span class="input-group-addon"><b>%</b></span>
                        </div>
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="filename" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("filename") }}</strong>
                        <i class="fa fa-info-circle" aria-hidden="true" @click="$modal.show('info-setting-modal')"></i>
                    </label>

                    <validation
                        :validator="validator"
                        label="filename"
                        class="col-xs-12 col-sm-9"
                    >
                        <div class="input-group">
                            <input
                                v-model="form.filename"
                                id="filename"
                                type="text"
                                class="form-control"
                            >
                            <span class="input-group-addon"><b>.pdf</b></span>
                        </div>
                    </validation>
                </div>
            </div>

            <hr>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <div class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("currency") }}</strong>
                    </div>

                    <validation
                        :validator="validator"
                        label="currency_id"
                        class="col-xs-12 col-sm-9"
                    >
                        <multiselect
                            v-model="currencyList"
                            track-by="id"
                            label="code"
                            :options="getCurrencies"
                            :allow-empty="false"
                            :searchable="true"
                            :max-height="160"
                            @select="selectCurrency"
                        />
                    </validation>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <div class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("moneyFormat") }}</strong>
                    </div>

                    <validation
                        :validator="validator"
                        label="money_format"
                        class="col-xs-6 col-sm-4"
                    >
                        <div class="row">
                            <label class="col-xs-6">
                                <input type="text" class="form-control text-center" v-model="form.money_format[0]">
                            </label>
                            <label class="col-xs-6">
                                <input type="text" class="form-control text-center" v-model="form.money_format[1]">
                            </label>
                        </div>
                    </validation>

                    <label class="col-xs-6 col-sm-3">
                        <input-money class="form-control text-center" v-model="money"
                                     :decimal="form.money_format[1]"
                                     :thousands="form.money_format[0]"
                                     :disabled="true"/>
                    </label>
                </div>
            </div>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12">
                    <label for="date_format" class="col-xs-12 col-sm-3 control-label text-align-right">
                        <strong>{{ $t("dateFormat") }}</strong>
                        <i class="fa fa-info-circle" aria-hidden="true" @click="$modal.show('info-setting-modal')"></i>
                    </label>

                    <validation class="col-xs-6 col-sm-4" :validator="validator" label="date_format">
                        <input id="date_format" type="text" class="form-control text-center" v-model="form.date_format">
                    </validation>

                    <label class="col-xs-6 col-sm-3">
                        <input type="text" class="form-control text-center" v-model="exampleDateFormat" disabled>
                    </label>
                </div>
            </div>

            <hr>

            <div class="manage-wrapper-item row">
                <div class="col-sm-12 col-xs-12 text-align-right">
                    <theme-button-success type="submit" class="btn btn-md" :disabled="isLoading">
                        {{ $t("update") }}
                    </theme-button-success>

                    <theme-button-warning type="button" class="btn btn-md" @click.native="setSettings">
                        {{ $t("reset") }}
                    </theme-button-warning>
                </div>
            </div>
        </form>
    </theme-manage-container>
</template>

<script>
    import {mapGetters}         from 'vuex'

    import MixinForm            from '@mixins/form'
    import Validation           from '@views/components/Validation'
    import ButtonFile           from "@views/elements/button/ButtonFile"
    import InputMoney           from "@views/components/InputMoney"
    import ThemeManageContainer from "@views/layouts/theme/ThemeManageContainer";
    import ThemeButtonSuccess   from "@views/layouts/theme/buttons/ThemeButtonSuccess";
    import ThemeButtonWarning   from "@views/layouts/theme/buttons/ThemeButtonWarning";

    export default {
        name: "bill-settings",
		components: {
			InputMoney,
			Validation,
			ButtonFile,
			ThemeManageContainer,
			ThemeButtonSuccess,
			ThemeButtonWarning
		},
		mixins: [
			MixinForm
		],
        data() {
            return {
                fontList: null,
                currencyList: null,

                form: {
                    id:             null,
                    currency_id:    null,
                    font_id:        null,

                    creator:        null,
                    author:         null,
                    title:          null,
                    subject:        null,
                    keywords:       null,

                    logo:           null,

                    filename:       null,
                    fee:            null,

					phone:          null,
					email:          null,
					web:            null,
					postcode:       null,
					street:         null,
					city:           null,

                    date_format:    null,
                    money_format:   [],
					bill_settings:  {}

                },
                money:              12345678.99,
            }
        },
        computed: {
            ...mapGetters({
                getFonts:       'default/getFonts',
                getCurrencies:  'default/getCurrencies',
                getUserProfile: 'user/getUserProfile',
                getSettings:    'settings/getSettings',
            }),
            exampleDateFormat() {
                if (this.form.date_format) {
                    return this.$moment().format(this.form.date_format)
                }

                return 'Invalid format';
            }
        },
        mounted() {
            this.setSettings();
        },
        methods: {
			setSettings() {
				this.form = Object.assign({}, this.form,  this.getSettings);
				this.form.money_format = [].concat(this.form.money_format);
				this.form.bill_settings = Object.assign({}, this.getSettings.bill_settings);

				this.fontList = this.getFonts.find(item => item.id === this.form.font_id);
				this.currencyList = this.getCurrencies.find(item => item.id === this.form.currency_id);
			},

            selectFont(value) {
                this.form.font_id = value.id;
            },

            selectCurrency(value) {
                this.form.currency_id = value.id;
            },

            uploadFile(e) {
                let file = e.target.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = e => this.form.logo = e.target.result;

                    reader.readAsDataURL(file);
                }
            },

            updateBillSetting() {
                if ((`${this.form.money_format[0]}`).trim() === "" || (`${this.form.money_format[1]}`).trim() === "") {
                    this.setValidator({
                        errors: {money_format: ["The money format field is required."]}
                    });

                    return this.$notify({type:'error', text: this.$t('invalid_money_format')});
                }

                const form = Object.assign({}, this.form);

                if (
                    form.money_format[0] === this.getSettings.money_format[0] &&
                    form.money_format[1] === this.getSettings.money_format[1]
                ) {
                    delete form.money_format;
                }

                this.defaultResponse();
                this.$api.tenants.updateSettings(form);
            },

        }
    }
</script>

<style lang="scss">
    #bill-setting {
        hr {
            margin-top: 3px;
            margin-bottom: 8px;
            border: 0;
            border-top: 1px solid #b9b5b5;
        }

        .input-group-addon {
            min-width: 60px;
        }

        .fa-info-circle {
            cursor: pointer;
        }
    }
    .bill-settings-logo {
        &-holder {
            margin-bottom: 20px;
        }

        &-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 10px;

            label {
                flex-grow: 1;
            }
        }
    }
</style>
