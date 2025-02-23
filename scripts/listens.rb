require "erb"
require "json"
require "open-uri"
require "base64"
include ERB::Util

template_location = File.join(File.dirname(__FILE__), "./templates/listens.svg.erb")
template = ERB.new(File.read(template_location))
template.location = template_location

listens = []

URI.open("https://api.listenbrainz.org/1/user/fkeiler/listens?count=4") do |f|
    json_body = JSON.load(f)
    listens = json_body["payload"]["listens"].map do |listen|
        caa_release_mbid = listen.dig("track_metadata", "mbid_mapping", "caa_release_mbid")
        cover = "data:image/svg+xml,%3Csvg viewBox='0 0 32 32' height='16' width='16' id='svg1' xmlns='http://www.w3.org/2000/svg' %3E%3Crect width='100%25' height='100%25' fill='white' /%3E%3Cg color='%23bebebe' transform='matrix(1.0005721,0,0,0.99949466,-1053.603,326.83475) translate(8, 8)' id='g1' style='fill:%23555761;stroke-width:0.999967'%3E%3Cpath d='m 1066.37,-327 -7.3726,1.906 c -0.554,0.149 -1,0.74 -1,1.313 v 9.094 c -0.626,-0.32246 -1.3529,-0.40087 -2.0334,-0.22 -1.35,0.351 -2.196,1.485 -1.907,2.532 0.29,1.047 1.619,1.632 2.969,1.281 1.076,-0.28 1.8531,-1.071 1.9711,-1.906 v -8.594 l 6.996,-1.875 v 6.781 c -0.6265,-0.32204 -1.3495,-0.39974 -2.0301,-0.218 -1.35,0.35 -2.196,1.484 -1.906,2.531 0.289,1.047 1.619,1.632 2.968,1.281 1.077,-0.28 1.8496,-1.071 1.9676,-1.906 l 4e-4,-11.188 c 0,-0.43 -0.265,-0.751 -0.624,-0.812 z' fill='%23666' overflow='visible' style='fill:%23555761;stroke-width:0.999681;marker:none' id='path1' /%3E%3C/g%3E%3C/svg%3E"
        unless caa_release_mbid.nil?
            cover = URI.open("https://coverartarchive.org/release/%{mbid}/front-250" % { :mbid => caa_release_mbid }, "rb") do |caa|
                "data:%{mime_type};base64,%{base64}" % { :mime_type => caa.content_type, :base64 => Base64.strict_encode64(caa.read) }
            end
        end
        { :artist => listen["track_metadata"]["artist_name"], :title => listen["track_metadata"]["track_name"], :image => cover }
    end
    puts template.result
end

#listens = [ { :artist => "Test Artist", :title => "Test Title" } ]

# puts template.result