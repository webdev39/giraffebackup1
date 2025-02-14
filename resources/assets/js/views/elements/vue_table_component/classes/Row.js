import moment       from 'moment';
import http         from '@utils/http';

export default class Row {
    constructor(data, columns) {
        this.data = data;
        this.columns = columns;
    }

    getValue(columnName) {
        return http.get(this.data, columnName);
    }

    getColumn(columnName) {
        return this.columns.find(column => column.show === columnName);
    }

    getFilterableValue(columnName) {
        const value = this.getValue(columnName);

        if (! value) {
            return '';
        }

        return value.toString().toLowerCase();
    }

    getSortableValue(columnName) {
        const dataType = this.getColumn(columnName).dataType;

        let value = this.getValue(columnName);

        if (value === undefined || value === null) {
            return '';
        }

        if (value instanceof String) {
            value = value.toLowerCase();
        }

        if (dataType.startsWith('date')) {
            const format  = dataType.replace('date:', '');

            return moment(value, format).format('YYYYMMDDHHmmss');
        }

        if (dataType === 'numeric') {
            return value;
        }

        return value.toString();
    }

    passesFilter(filter) {
        return this.columns
            .filter(column => column.isFilterable())
            .map(column => this.getFilterableValue(column.getFilterFieldName()))
            .filter(filterableValue => filterableValue.indexOf(filter.toLowerCase()) >= 0)
            .length;
    }
}
