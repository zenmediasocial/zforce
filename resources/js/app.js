import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('terminal', () => ({
        typingLines: [],
        isTyping: false,

        init() {
            this.$watch('isTyping', value => {
                if (this.$wire) {
                    this.$wire.isTyping = value;
                }
            });
        },

        async typeLines(lines) {
            if (this.isTyping) return;

            this.isTyping = true;
            this.typingLines = [];

            for (const lineText of lines) {
                const id = Date.now() + Math.random();
                const lineClass = lineText.startsWith('WARNING') ? 'warning' :
                                 lineText.startsWith('⚠') ? 'priority-alert' :
                                 lineText.startsWith('━') ? 'separator' :
                                 lineText.startsWith('C:\\>') ? 'bright' : '';

                const newLine = { id, text: '', class: lineClass, fullText: lineText };
                this.typingLines.push(newLine);

                // Character-by-character typing
                for (let i = 0; i <= lineText.length; i++) {
                    newLine.text = lineText.slice(0, i);
                    await new Promise(r => setTimeout(r, 4));
                }

                await new Promise(r => setTimeout(r, 15));
                this.scrollToBottom();
            }

            // Dispatch event to add lines permanently to Livewire
            const permanentLines = this.typingLines.map(l => ({
                text: l.fullText,
                class: l.class,
            }));

            this.$dispatch('lines-typed', { lines: permanentLines });

            // Clear typing lines after a brief delay
            await new Promise(r => setTimeout(r, 100));
            this.typingLines = [];
            this.isTyping = false;
            this.scrollToBottom();
        },

        scrollToBottom() {
            if (this.$refs.output) {
                this.$refs.output.scrollTop = this.$refs.output.scrollHeight;
            }
        },
    }));
});

Alpine.start();
