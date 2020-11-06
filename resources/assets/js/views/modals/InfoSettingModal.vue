<template>
    <modal :name="$options.name" :id="$options.name" height="auto" width="50%" :pivotY="0.2" :adaptive="true" :scrollable="true" @before-open="beforeOpen" @before-close="beforeClose" style="z-index:1001" >
        <modal-wrapper :name="$options.name" color="white">
            <template slot="body">
                <h4> {{ $t('tags_in_bill_template_and_filename') }} </h4>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ $t('tag') }}</th>
                            <th>{{ $t('description') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(description, tag) in tags">
                            <td>{{ tag }}</td>
                            <td>{{ description }}</td>
                        </tr>
                    </tbody>
                </table>

                <h4>Datetime format <i>(Tokens are case-sensitive.)</i></h4>

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>{{ $t('format') }}</th>
                        <th>{{ $t('example') }}</th>
                        <th>{{ $t('description') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in moment">
                        <td>{{ item[0] }}</td>
                        <td>{{ item[1] }}</td>
                        <td>{{ item[2] }}</td>
                    </tr>
                    </tbody>
                </table>
            </template>

            <template slot="footer">
                <button type="submit" class="btn btn-warning" @click="$modal.hide($options.name)">
                    {{ $t('close') }}
                </button>
            </template>
        </modal-wrapper>
    </modal>
</template>

<script>
    import ModalWrapper from "@assets/js/views/layouts/ModalWrapper";

    export default {
        name: "info-setting-modal",
        data() {
            return {
                tags: {
                    '{currency}':           'Currency (ISO 4217)',
                    '{logo}':               'Company logo',
                    '{fee}':                'Fee in (%)',

                    '{bill.id}':            'Bill ID',
                    '{bill.invoice_number}': 'Bill Invoice Number',
                    '{bill.created_at}':    'Bill creation date',

					'{author}': 'Company name',

					'{email}': 'Company email',
					'{phone}': 'Company phone',
					'{postcode}': 'Company postcode',
					'{street}': 'Company street',
					'{city}': 'Company city',
					'{web}': 'Company web',

                    '{customer.id}':        'Customer ID',
                    '{customer.name}':      'Customer name',
                    '{customer.contact}':   'Customer contact',
                    '{customer.street}':    'Customer street',
                    '{customer.house}':     'Customer house',
                    '{customer.postcode}':  'Customer postcode',
                    '{customer.city}':      'Customer city',
                    '{customer.country}':   'Customer country',
                },
                moment: [
                    ['YYYY', '2014', '4 or 2 digit year'],
                    ['YY', '14', '2 digit year'],
                    ['Y', '-25', 'Year with any number of digits and sign'],
                    ['Q', '1..4', 'Quarter of year. Sets month to first month in quarter.'],
                    ['M MM', '1..12', 'Month number'],
                    ['MMM MMMM', 'Jan..December', 'Month name in locale set by moment.locale()'],
                    ['D DD', '1..31', 'Day of month'],
                    ['Do', '1st..31st', 'Day of month with ordinal'],
                    ['DDD DDDD', '1..365', 'Day of year'],
                    ['X', '1410715640.579', 'Unix timestamp'],
                    ['x', '1410715640579', 'Unix ms timestamp'],

                    ['gggg', '2014', 'Locale 4 digit week year'],
                    ['gg', '14', 'Locale 2 digit week year'],
                    ['w ww', '1..53', 'Locale week of year'],
                    ['e', '0..6', 'Locale day of week'],
                    ['ddd dddd', 'Mon...Sunday', 'Day name in locale set by moment.locale()'],
                    ['GGGG', '2014', 'ISO 4 digit week year'],
                    ['GG', '14', 'ISO 2 digit week year'],
                    ['W WW', '1..53', 'ISO week of year'],
                    ['E', '1..7', 'ISO day of week'],

                    ['H HH', '0..23', 'Hours (24 hour time)'],
                    ['h hh', '1..12', 'Hours (12 hour time used with a A.)'],
                    ['k kk', '1..24', 'Hours (24 hour time from 1 to 24)'],
                    ['a A', 'am pm', 'Post or ante meridiem (Note the one character a p are also considered valid)'],
                    ['m mm', '0..59', 'Minutes'],
                    ['s ss', '0..59', 'Seconds'],
                    ['S SS SSS', '0..999', 'Fractional seconds'],
                    ['Z ZZ', '+12:00', 'Offset from UTC as +-HH:mm, +-HHmm, or Z'],
                ]
            }
        },
        components: {
            ModalWrapper
        },
        methods: {
            beforeOpen(event) {
                //
            },
            beforeClose(event) {
                //
            },
            closeModal() {
                this.$modal.hide(this.$options.name)
            }
        }
    }
</script>

<style lang="scss">
    //
</style>
