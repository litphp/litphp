/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

const React = require('react');

class Footer extends React.Component {
    docUrl(doc, language) {
        const baseUrl = this.props.config.baseUrl;
        return `${baseUrl}docs/${language ? `${language}/` : ''}${doc}`;
    }

    pageUrl(doc, language) {
        const baseUrl = this.props.config.baseUrl;
        return baseUrl + (language ? `${language}/` : '') + doc;
    }

    render() {
        return (
            <footer className="nav-footer" id="footer">
                <section className="sitemap">
                    <a href={this.props.config.baseUrl} className="nav-home">
                        {this.props.config.footerIcon && (
                            <img
                                src={this.props.config.baseUrl + this.props.config.footerIcon}
                                alt={this.props.config.title}
                                width="66"
                                height="58"
                            />
                        )}
                    </a>
                    <div>
                        <h5>Docs</h5>
                        <a href={this.docUrl('.', this.props.language)}>
                            QuickStart
                        </a>
                        {/*<a href={this.docUrl('doc2.html', this.props.language)}>*/}
                        {/*Guides (or other categories)*/}
                        {/*</a>*/}
                        {/*<a href={this.docUrl('doc3.html', this.props.language)}>*/}
                        {/*API Reference (or other categories)*/}
                        {/*</a>*/}
                    </div>
                    <div>
                        <h5>Community</h5>
                        {/*<a href={this.pageUrl('users.html', this.props.language)}>*/}
                        {/*User Showcase*/}
                        {/*</a>*/}
                        {/*<a*/}
                        {/*href="http://stackoverflow.com/questions/tagged/"*/}
                        {/*target="_blank"*/}
                        {/*rel="noreferrer noopener">*/}
                        {/*Stack Overflow*/}
                        {/*</a>*/}
                        <a
                            href="https://gitter.im/litphp/Lobby?utm_source=share-link&utm_medium=link&utm_campaign=share-link"
                            target="_blank"
                            rel="noreferrer noopener">
                            <img src="//badges.gitter.im/litphp.png" alt="Chat on gitter"/>
                        </a>
                    </div>
                    <div>
                        <h5>More</h5>
                        <a href={`${this.props.config.baseUrl}blog`}>Blog</a>
                        <a href="https://github.com/litphp">GitHub</a>
                    </div>
                </section>
                <section className="copyright">{this.props.config.copyright}</section>
            </footer>
        );
    }
}

module.exports = Footer;
