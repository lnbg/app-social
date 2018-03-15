export default {
    methods: {
        formatDate: function (date) {
            if (date == undefined) {
                return String.empty
            }
            let fixDateForAllBrowsers = dateString => dateString.replace(/-/g, '/');
            let d = new Date(fixDateForAllBrowsers(date))
            let month = '' + (d.getMonth() + 1)
            let day = '' + d.getDate()
            let year = d.getFullYear()

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            return [year, month, day].join('-');
        },
        dateCompare: (DateA, DateB) => {
            var a = new Date(DateA);
            var b = new Date(DateB);

            var msDateA = Date.UTC(a.getFullYear(), a.getMonth()+1, a.getDate());
            var msDateB = Date.UTC(b.getFullYear(), b.getMonth()+1, b.getDate());
      
            if (parseFloat(msDateA) < parseFloat(msDateB))
                return -1;  // less than
            else if (parseFloat(msDateA) == parseFloat(msDateB))
                return 0;  // equal
            else if (parseFloat(msDateA) > parseFloat(msDateB))
                return 1;  // greater than
            else
                return null;  // error
        },
        diffDays(dateA, dateB) {
            return (dateA - dateB) / 86400000;
        },
    }
}