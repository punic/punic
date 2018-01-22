FROM alpine:3.6

RUN apk upgrade -U && \
    apk add --update --no-cache \
        subversion \
        openjdk8 \
        apache-ant \
        php7 \
        php7-json

ENV PATH=$PATH:/punic/bin

WORKDIR /punic

CMD /punic/bin/build.sh
