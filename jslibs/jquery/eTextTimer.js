/**
 *
 * Simple text timer js class by Epsilon
 *
 * (Original version by unknown French programmer)
 *
 * Input options defaults:
 *   targetDate: ""
 *   nowDate: ""
 *   countActive: true
 *   countStepper: -1
 *   leadingZero: true
 *   dstprefix: ''
 */
function eTextTimer2(opts) {

    this.calcage = function(secs, num1, num2) {
        s = ((Math.floor(secs/num1))%num2).toString();
        if (this.options[0].leadingZero && s.length < 2)
            s = "0" + s;
        return s;
    }

    this.calcageNLZ = function(secs, num1, num2) {
        return ((Math.floor(secs/num1))%num2).toString();
    }

    this.countBack = function() {

        this.secs --;
        secs = this.secs;

        if (secs < 0) {
            secs = 0;
        }

        pfx = this.options[0].dstprefix;

        $('.' + pfx + 'd').html(this.calcageNLZ(secs, 86400, 100000));
        $('.' + pfx + 'h').html(this.calcage(secs, 3600, 24));
        $('.' + pfx + 'm').html(this.calcage(secs, 60, 60));
        $('.' + pfx + 's').html(this.calcage(secs, 1, 60));

        if (this.options[0].countActive) {
            setTimeout(
                (function(obj){
                    return function() {
                        obj.countBack();
                    };
                })(this),
                this.setTimeOutPeriod
            );
        }
    };

    // Initializer
    this.options = new Array({
        targetDate: "12/31/2030 5:00 AM",
        nowDate: "",
        countActive: true,
        countStepper: -1,
        leadingZero: true,
        dstprefix: ""
    });

    for (var key in opts) {
        this.options[0][key] = opts[key];
    }

    this.options[0].countStepper = Math.ceil(this.options[0].countStepper);
    if (this.options[0].countStepper == 0) {
        this.options[0].countActive = false;
    }

    this.setTimeOutPeriod = (Math.abs(this.options[0].countStepper)-1)*1000 + 990;
    var dthen = new Date(this.options[0].targetDate);
    var dnow = new Date(this.options[0].nowDate);
    if (this.options[0].countStepper > 0) {
        ddiff = new Date(dnow - dthen);
    } else {
        ddiff = new Date(dthen - dnow);
    }
    this.secs = Math.floor(ddiff.valueOf() / 1000);
    this.countBack();
}
