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
            context = nil
            if self.respond_to?"name"
                context = self
            else
                context = self.class
            end

            context.name.light_cyan + "##{source}\t  ".cyan
        end

        def get_warn_context source
            context = nil
            if self.respond_to?"name"
                context = self
            else
                context = self.class
            end

            context.name.light_red + "##{source}\t  ".light_red
        end
    end
end
