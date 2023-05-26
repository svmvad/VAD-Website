import {
    $,
    $$,
    addClass,
    attr,
    closest,
    css,
    hasClass,
    observeMutation,
    observeResize,
    offset,
    removeClass,
    toPx,
} from 'uikit-util';

const Section = {
    connected() {
        const selector = '.tm-header ~ * > [class*="uk-section"]';
        this.section = $(selector);
        if (!this.section) {
            observeMutation(
                document.body,
                (records, observer) => {
                    this.section = $(selector);
                    if (this.section) {
                        observer.disconnect();
                        this.$emit();
                    }
                },
                { childList: true, subtree: true }
            );
        }
    },
};

export const Header = {
    mixins: [Section],

    connected() {
        observeResize(this.$el, () => this.$emit('resize'));
    },

    update: [
        {
            read() {
                if (!getModifier(this.section) || !this.$el.offsetHeight) {
                    return false;
                }

                return { height: this.$el.offsetHeight };
            },

            write({ height }) {
                if (!hasClass(this.$el, 'tm-header-overlay')) {
                    const modifier = getModifier(this.section);

                    addClass(this.$el, 'tm-header-overlay');
                    addClass(
                        $$('.tm-headerbar-top, .tm-headerbar-bottom, .js-toolbar-transparent'),
                        `uk-${modifier}`
                    );
                    removeClass(
                        $$('.tm-headerbar-top, .tm-headerbar-bottom'),
                        'tm-headerbar-default'
                    );
                    removeClass(
                        $('.js-toolbar-transparent.tm-toolbar-default'),
                        'tm-toolbar-default'
                    );

                    if (!$('[uk-sticky]', this.$el)) {
                        addClass(
                            $('.uk-navbar-container', this.$el),
                            `uk-navbar-transparent uk-${modifier}`
                        );
                    }
                }

                css($('.tm-header-placeholder', this.section), { height });
            },

            events: ['resize'],
        },
    ],
};

export const Sticky = {
    mixins: [Section],

    update: {
        read() {
            const modifier = getModifier(this.section);

            if (!modifier || !closest(this.$el, '[uk-header]')) {
                return;
            }

            this.animation = 'uk-animation-slide-top';
            this.clsInactive = `uk-navbar-transparent uk-${modifier}`;

            if (!this.active) {
                addClass(this.selTarget, this.clsInactive);
            }

            return {
                start:
                    this.section.offsetHeight <= toPx('100vh')
                        ? offset(this.section).bottom
                        : offset(this.section).top + 300,
            };
        },

        events: ['resize'],
    },
};

function getModifier(el) {
    return attr(el, 'tm-header-transparent');
}
