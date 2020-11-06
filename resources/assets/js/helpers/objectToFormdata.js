function isUndefined (value) {
    return value === undefined
}

function isNull (value) {
    return value === null
}

function isBoolean (value) {
    return typeof value === 'boolean'
}

function isObject (value) {
    return value === Object(value)
}

function isArray (value) {
    return Array.isArray(value)
}

function isNumber (value) {
    return value !== null &&
        typeof value.size === 'number';
}

function isBlob (value) {
    return value !== null &&
        typeof value.size === 'number' &&
        typeof value.type === 'string' &&
        typeof value.slice === 'function'
}

function isFile (value) {
    return isBlob(value) &&
        typeof value.lastModified === 'number' &&
        typeof value.name === 'string'
}

function isDate (value) {
    return value instanceof Date
}

function objectToFormdata (obj, name, fd) {
    fd = fd || new FormData();

    if (isUndefined(obj)) {
        return fd
    } else if (isArray(obj)) {
        obj.forEach((value, index) => {
            let key = name + '[' + index + ']';

            objectToFormdata(value, key, fd)
        })
    } else if (isObject(obj) && !isFile(obj) && !isDate(obj)) {
        Object.keys(obj).forEach(function (prop) {
            let value = obj[prop];

            if (isArray(value)) {
                while (prop.length > 2 && prop.lastIndexOf('[]') === prop.length - 2) {
                    prop = prop.substring(0, prop.length - 2)
                }
            }

            let key = name ? (name + '[' + prop + ']') : prop;

            objectToFormdata(value, key, fd)
        })
    } else {
        if (isNull(obj)) {
            return;
        } else if (isBoolean(obj)) {
            let value = (obj === true) ? '1' : '0';

            fd.append(name, value);
        } else {
            fd.append(name, obj);
        }
    }

    return fd
}

module.exports = objectToFormdata;