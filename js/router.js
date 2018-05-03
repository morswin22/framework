class Router {
    constructor() {
        this.wrapper = undefined;
        this.last = -1;
    }

    setDOM(wrapper) {
        this.wrapper = wrapper;
        this.buttons = this.wrapper.children();
        let range = [];
        this.buttons.each((i,e) => {
            range.push($(e).html());
        });
        this.range = range;
        this.selected = -1;
        this.buttons.on('click', (e)=>{
            let self = $(e.target);
            this.wrapper.find('.active').removeClass('active');
            this.selected = this.range.indexOf(self.html());
            if(this.selected == this.last) {
                this.selected = -1;
            } else {
                self.addClass('active');
            }
            this.switch();
        });
        return this;
    }

    setPages(array) {
        this.pages = array;
        return this;
    }

    setDefault(elem) {
        this.default = elem;
        return this;
    }

    switch() {
        if (this.last > -1) {
            this.pages[this.last].hide();
        }
        if (this.selected > -1) {
            if (this.default) this.default.hide();
            this.pages[this.selected].show();
        } else {
            if (this.default) this.default.show();
        }
        this.last = this.selected;
    }
}