let util = {};

util.title = function (title) {
    title = title ? title + ' - Police' : 'Police';
    window.document.title = title;
};

export default util;
