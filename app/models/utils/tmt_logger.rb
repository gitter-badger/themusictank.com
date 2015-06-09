module Utils
    module TmtLogger

    	def log message
            Rails.logger.info get_prefix + get_type_info + get_info_context(caller[0][/`.*'/][1..-2]) + message
    	end

    	def warn message
            Rails.logger.info get_prefix + get_type_warn + get_warn_context(caller[0][/`.*'/][1..-2]) + message
    	end

        protected

        def get_prefix
            "  [TMT]  ".light_white
        end

        def get_type_info
            "[INFO]  ".light_green
        end

        def get_type_warn
            "[WARN]  ".light_red
        end

        def get_info_context source
            contextualize.name.light_cyan + "##{source}\t  ".cyan
        end

        def get_warn_context source

            contextualize.name.light_red + "##{source}\t  ".light_red
        end

        private

        def contextualize
            if self.respond_to?"name"
                self
            else
                self.class
            end
        end
    end
end
