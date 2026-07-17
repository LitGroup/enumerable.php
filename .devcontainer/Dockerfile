#
# Copyright (c) 2015-2026 Roman Shamritskiy <roman@litgroup.ru>
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is furnished
# to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#

ARG PHP_VERSION="8"

FROM php:${PHP_VERSION}-cli

ARG USERNAME=developer
ARG USER_UID=1000
ARG USER_GID=$USER_UID

USER root

# Install system packages
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        libzip-dev \
        uuid-dev \
        libicu-dev \
    && apt-get install -y --no-install-recommends \
        bash-completion \
        build-essential \
        cloc \
        curl \
        git \
        less \
        locales \
        nano \
        openssh-client \
        sudo \
        tree \
        unzip \
        vim \
        wget \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Generate locales
RUN sed -i 's/^# *\(en_US.UTF-8\)/\1/' /etc/locale.gen && locale-gen

# Set shell command aliases
RUN echo "alias ll='ls -l'" > /etc/profile.d/aliases.sh

# Configure PHP
RUN cp -f /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Install misc PHP Extension
RUN docker-php-ext-install -j$(nproc) zip intl \
    && docker-php-source delete \
    && rm -rf /tmp/* /var/tmp/*

# Install XDebug
RUN docker-php-source extract \
    && cd /tmp \
    && wget https://xdebug.org/files/xdebug-3.5.0alpha3.tgz \
    && tar -xzf xdebug-3.5.0alpha3.tgz \
    && cd xdebug-3.5.0alpha3 \
    && phpize \
    && ./configure --enable-xdebug \
    && make \
    && make install \
    && docker-php-ext-enable xdebug \
    && docker-php-source delete \
    && rm -rf /tmp/* /var/tmp/*

# Install Composer
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Create user and configure sudo
RUN useradd --create-home --uid ${USER_UID} --user-group --shell /bin/bash ${USERNAME}
RUN echo "${USERNAME} ALL=(root) NOPASSWD:ALL" > /etc/sudoers.d/${USERNAME} \
    && chmod 0440 /etc/sudoers.d/${USERNAME}

USER ${USERNAME}
