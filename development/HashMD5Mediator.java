import org.apache.synapse.MessageContext;
import org.apache.synapse.mediators.AbstractMediator;
import org.apache.commons.codec.digest.DigestUtils;

public class HashMD5Mediator extends AbstractMediator {
    public boolean mediate(MessageContext mc) {
        String password = (String) mc.getProperty("usr_password");
        if (password != null && !password.isEmpty()) {
            String md5Hash = DigestUtils.md5Hex(password);
            mc.setProperty("usr_password_md5", md5Hash);
        }
        return true;
    }
}
