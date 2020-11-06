import config from '@config';

export const _setDocumentTitle = (newTitle) => {
    if (!newTitle) {
        return;
    }
    document.title = `${newTitle} - ${config.appName}`;
};

export const _setDefaultDocumentTitle = () => {
    document.title = config.appName;
};
