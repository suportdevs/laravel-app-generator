class Shortcodes {
    static get pluginName() {
        return 'Shortcodes';
    }

    init() {
        const editor = this.editor; // Access the editor instance
        console.log('Shortcodes plugin initialized');

        // Add a button to the toolbar
        editor.ui.componentFactory.add('shortcodes', locale => {
            const view = new editor.ui.button.ButtonView(locale);

            view.set({
                label: 'Insert Shortcode',
                withText: true,
                tooltip: true,
            });

            view.on('execute', () => {
                editor.model.change(writer => {
                    editor.model.insertContent(writer.createText('[shortcode_placeholder]'));
                });
            });

            return view;
        });
    }
}

// Export or attach to global object for usage
window.Shortcodes = Shortcodes;
